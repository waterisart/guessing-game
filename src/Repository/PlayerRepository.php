<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use App\Entity\Player;
use App\Entity\Round;

/**
 * @extends ServiceEntityRepository<Player>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function add(Player $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Player $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $player, string $newHashedPassword): void
    {
        if (!$player instanceof Player) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($player)));
        }

        $player->setPassword($newHashedPassword);

        $this->add($player, true);
    }

//    /**
//     * @return Player[] Returns an array of Player objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

   public function findOneByUsername(string $username): ?Player
   {
       return $this->createQueryBuilder('u')
           ->andWhere('u.username = :username')
           ->setParameter('username', $username)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

   public function addRoundToPlayer(Round $round, Player $player, bool $flush = false): void 
   {
       $player->addRound($round);
       $this->getEntityManager()->persist($player);

       if ($flush) {
           $this->getEntityManager()->flush();
       }
   }
}
