<?php


namespace App\Controller\Admin;



use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 *
 * @Route("/admin/categorie")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render(
            'admin/category/index.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * L'id est est optionnel et vaut null par défaut :
     * si on ne passe pas d'id dans l'url, on est en création,
     * si on passe un id, on est en modification
     * @Route("/edition/{id}", defaults={"id": null})
     */
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        $id
    ) {
        if (is_null($id)) { // création
            $category = new Category();
        } else { // modification
            $category = $categoryRepository->find($id);
        }

        // création du formulaire relié à la catégorie
        $form = $this->createForm(CategoryType::class, $category);

        // le formulaire analyse la requête
        // et sette les valeurs des attributs Category avec les valeurs
        // avec les valeurs saisies dans le formulaire s'il a été envoyé
        $form->handleRequest($request);

        dump($category);

        // si le formulaire a été soumis
        if ($form->isSubmitted()) {
            // si les validations à partir des annotations @Assert
            // dans l'entité Category sont ok
            if ($form->isValid()) {
                // quand on va appeler la méthode flush(),
                // la catégorie devra être enregistrée en bdd
                $entityManager->persist($category);
                // enregistrement en bdd
                $entityManager->flush();

                // enregistrement dans la session d'un message pour affichage unique
                $this->addFlash('success', 'La catégorie est enregistrée');
                // redirection vers la page de liste
                return $this->redirectToRoute('app_admin_category_index');
            }

        }

        return $this->render(
            'admin/category/edit.html.twig',
            [
                // pour pouvoir utiliser le formulaire dans le template
                'form' => $form->createView()
            ]
        );
    }
}