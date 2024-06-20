<?php


namespace App\EventSubscriber;

use App\Entity\Notif;
use App\Entity\Post;
use App\Entity\User;
use Doctrine;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;


class NotificationCreator implements EventSubscriber

{

    /**
     * NotificationCreator constructor.
     * @param  $manager
     */
    private EntityManagerInterface $manager;

    private ?Post $lastLikedPost = null;
    private ?User $lastLiker = null;

    /**
     * NotificationCreator constructor.
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::onFlush
        ];
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {

        if ($eventArgs->getObject() instanceof Post) {

            /** @var Post $post */
            $post = $eventArgs->getObject();

            // Si c'est un Post de type "commentaire"
            if (!empty($post->getParent())) {

                // ... et qu'on commente le Post d'un autre User
                if ($post->getAuthor() !== $post->getparent()->getAuthor()) {

                    $notifComments = new Notif();
                    $notifComments->setOrigin($post->getAuthor());
                    $notifComments->setUser($post->getParent()->getAuthor());
                    $notifComments->setPostorigin($post);
                    $notifComments->setTextContent('vient de commenter votre post');
                    $this->manager->persist($notifComments);

                    $this->manager->flush();
                }
            }
        }
    }



    public function onFlush(OnFlushEventArgs $args) {

        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof User) {

                $liker = $entity;
                $lastLikedPost = $liker->getLikedPosts()->last();

                // Le compte des posts likés dans l'entité PHP
                $currentLikeCount = $liker->getLikedPosts()->count();

                // Le compte des posts likés dans la Base de données actuellement
                $oldLikeCount = $em
                    ->getRepository(User::class)
                    ->countLikedPostsBy($liker);

                // S'il vient de liker un Post
                if ($currentLikeCount > $oldLikeCount) {

                    // on notifie les concernés
                    $notification = new Notif();
                    $notification->setOrigin($liker);
                    $notification->setUser($lastLikedPost->getAuthor());
                    $notification->setPostorigin($lastLikedPost);
                    $notification->setTextContent('vient de liker votre post');


                    $em->persist($notification);
                    $classMetadata = $em->getClassMetadata(Notif::class);
                    $uow->computeChangeSet($classMetadata, $notification);
                }
            }
        }
    }
}