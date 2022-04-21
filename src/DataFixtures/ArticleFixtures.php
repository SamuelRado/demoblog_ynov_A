<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // créer 3 catégories via faker

        for ($i = 1; $i <= 3; $i++) {
            $category = new Category;
            $category->setTitle($faker->sentence());

            $manager->persist($category);

            // créer entre 4 et 6 articles par catégorie

            for ($j = 1; $j <= mt_rand(4, 6); $j++) {
                $article = new Article;

                $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage("http://picsum.photos/250/150")
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($article);

                // créer entre 5 et 10 commentaires par article

                for ($k = 1; $k <= mt_rand(5, 10); $k++) {
                    $comment = new Comment;

                    $content = '<p>' . join('</p><p>', $faker->paragraphs(2)) . '</p>';

                    // $now = new \DateTime(); // date d'aujourd'hui
                    // $interval = $now->diff($article->getCreatedAt());   // intervalle entre aujd et la date de création de l'article
                    // $days = $interval->days;    // récupération de l'intervalle en jours

                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                        ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
