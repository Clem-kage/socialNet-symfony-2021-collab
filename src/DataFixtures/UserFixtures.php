<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * UserFixtures constructor.
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create("fr_FR");
    }

    public function load(ObjectManager $manager)
    {
        // créer une vingtaine d'utilisateurs fictifs mais réalistes
        $nbUsers = rand(18, 22);

        for ($i = 0; $i < $nbUsers; $i++) {
            $manager->persist($this->generateUser());
        }

        // On crée un utilisateur "fixe", pour utiliser toujours le même lors de nos tests
        $demoUser = $this->generateUser("demo", "Demo User");
        $manager->persist($demoUser);

        $manager->flush();
    }


    /**
     * Créé un utilisateur fictif aléatoire.
     * Cet utilisateur aura toujours pour mot de passe "azeaze"
     *
     * @param string|null $username - Le username de l'utilisateur à créer
     * @return User
     */
    private function generateUser(
        ?string $username = null,
        ?string $fullName = null
    ): User
    {

        $user = new User();

        if (empty($username)) {
            $username = $this->faker->userName;
        }

        $gender = rand(1, 100) <= 50 ? "male" : "female";

        $fullName = $fullName ?? $this->faker->name($gender);

        $email = $this->faker->email;
        $birth = $this->faker->dateTimeBetween('-80 years', '-18 years');
        $inscription = $this->faker->dateTimeThisDecade();

        // On doit absolument chiffrer le mot de passe de l'utilisateur !
        $realPassword = "azeaze";
        $hash = $this->encoder->encodePassword($user, $realPassword);

        $user->setUsername($username);
        $user->setEmail($email);
        $user->setGender($gender);
        $user->setFullName($fullName);
        $user->setBirthdate($birth);
        $user->setCreatedAt($inscription);
        $user->setPassword($hash);

        return $user;

    }
}
