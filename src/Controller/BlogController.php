<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Contact;
use App\Form\ArticleType;
use App\Form\ContactType;
use App\Form\RechercheType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render("blog/home.html.twig", [
            'title' => 'Bienvenue sur le blog',
            'age' => 27
        ]);
    }

    /**
     * @Route("/blog", name="app_blog")
     */
    // chaque route est associée à la méthode directement en dessous
    // une route possède deux arguments obligatoires : un chemin et un nom
    public function index(ArticleRepository $repo, Request $request): Response
    {
        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())    // si l'utilisateur fait une recherche
        {
            $data = $form->get('recherche')->getData(); // je récupère la saisie de l'utilisateur
            $articles = $repo->getArticlesByName($data);
        }
        else    // sinon, pas de recherche : je récupère TOUS les articles
        {
            $articles = $repo->findAll();
            // je récupère tous les articles que je stocke dans un tableau $articles
        }

        // toutes les méthodes d'un controller doivent renvoyer un objet de type Response
        // render() est une méthode qui permet d'afficher le contenu d'un template
        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
            'formRecherche' => $form->createView()
        ]);
    }

    /**
     * @Route("/blog/show/{id}", name="blog_show")
     */
    // {id} : paramètre de route pour créer une route paramétrée
    public function show(Article $article)
    {
        // symfony va essayer de trouver l'article correspondant à l'id dans la route grâce au ParamConverter
        return $this->render("blog/show.html.twig", [
            'article' => $article
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/edit/{id}", name="blog_edit")
     */
    public function form(Request $request, EntityManagerInterface $manager, Article $article = null)
    {
        // article = null signifie que si l'on va sur la route new alors $article = null
        // et si on est sur edit, alors l'article correspondra à l'id dans la route

        if (!$article)
        {
            $article = new Article;
            $article->setCreatedAt(new \DateTime());    // on ajoute la date à l'insertion
        }
        // la classe Request contient tous les données des superglobales
        // je crée un objet Article vide prêt à être rempli
        // dump($request);
        // dans la classe Request, l'objet request contient les données de $_POST
        // l'objet query contient les données de $_GET

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        // handleRequest() permet de faire certaines vérifications (la méthode du formulaire ?)
        // permet aussi de vérifier si les champs sont remplis
        // dump($article);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show', [
                'id' => $article->getId()
            ]);
            // après insertion de l'article, je me redirige vers la route blog_show
            // cette route a besoin du paramètre "id" : l'id de l'article que je viens d'insérer
        }
        return $this->render("blog/create.html.twig", [
            'formArticle' => $form->createView(),
            // createView() renvoie un objet permettant d'afficher le formulaire
            'editMode' => $article->getId() !== null
            // editMode = 1 si on est en édition
            // editMode = 0 si on est en création
        ]);
    }

    /**
     * @Route("/blog/contact", name="blog_contact")
     */
    public function contact(Request $request, EntityManagerInterface $manager)
    {
        $contact = new Contact;
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($contact);
            $manager->flush();
        }

        return $this->render("blog/contact.html.twig", [
            'formContact' => $form->createView()
        ]);
    }
}
