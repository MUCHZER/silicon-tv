<?php
namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Member;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AppController extends Controller
{
	
	/**
	 * @Route("/", name="home")
	 * @param Request $request
	 * @return Response
	 */
	public function home(Request $request)
	{
		$member = new Member();
		
		$form = $this->createFormBuilder($member)
            ->add('email', EmailType::class)
            ->add('name', TextType::class)
            ->add('imageFile', VichImageType::class, [
				'image_uri' => true,
            	'imagine_pattern' => 'squared_thumbnail'
			])
            ->add('bio', TextareaType::class, [
            	'attr' => [
            		'class' => 'textarea'
				],
			])
            ->add('url', UrlType::class, [
				'required'   => false,
			])
            ->add('facebook', UrlType::class, [
				'required'   => false,
			])
            ->add('twitter', TextType::class, [
				'required'   => false,
			])
            ->add('instagram', TextType::class, [
				'required'   => false,
			])
            ->add('linkedin', UrlType::class, [
				'required'   => false,
			])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
			->getForm();
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			/** @var Member $member */
			$member = $form->getData();
			
			$em->persist($member);
			$em->flush();
			
			return $this->redirectToRoute('valid', ['id' => $member->getId()]);
		}
		
		return $this->render('home.html.twig', [
			'form' => $form->createView()
		]);
	}
	
	/**
	 * @Route("/valid/{id}", name="valid")
	 * @param int $id
	 * @return Response
	 */
	public function valid(int $id)
	{
		$member = $this->getDoctrine()->getRepository(Member::class)->find($id);
		return $this->render('valid.html.twig', [
			'member' => $member
		]);
	}
	
	/**
	 * @Route("/dump", name="dump")
	 * @return JsonResponse
	 */
	public function dump()
	{
		$members = $this->getDoctrine()->getRepository(Member::class)->findAll();
		$encoders = [new JsonEncoder()];
		$normalizers = [new ObjectNormalizer()];
		
		$serializer = new Serializer($normalizers, $encoders);
		$json = $serializer->serialize($members, 'json');
		
		return new JsonResponse($json, 200, [], true);
	}
	
}