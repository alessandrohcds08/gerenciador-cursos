<?php


namespace Alura\Cursos\Controller;


use Alura\Cursos\Entity\Curso;
use Alura\Cursos\Helper\FlashMessageTrait;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Persistencia implements RequestHandlerInterface
{
    use FlashMessageTrait;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $descricao = filter_var($request->getParsedBody()['descricao'], FILTER_SANITIZE_STRING);
        $curso = new Curso();
        $curso->setDescricao($descricao);

        $id = isset($request->getQueryParams()['id']) ? filter_var($request->getQueryParams()['id'], FILTER_VALIDATE_INT) : '';

        if (!is_null($id) || $id != false || empty($id)) {
            $curso->setId($id);
            $this->entityManager->merge($curso);
            $this->defineMensagem('success','Curso atualizado com sucesso');
        } else {
            $this->entityManager->persist($curso);
            $this->defineMensagem('success','Curso criado com sucesso');
        }
        $this->entityManager->flush();

        return new Response(302, ['Location' => '/listar-cursos']);
    }
}