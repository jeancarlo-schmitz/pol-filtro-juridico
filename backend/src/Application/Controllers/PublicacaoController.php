<?php
namespace Application\Controllers;

use Application\Exceptions\Constants\HttpExceptionConstants;
use Application\RequestHandler;
use Application\Services\ApiManagerService;
use Application\Services\AuthService;
use Application\Services\PublicacaoService;
use Http\Request;
use Http\Response;
use Infrastructure\Utils\ValidationRules;

/**
 * @OA\Info(
 *     title="Api Restful AtitudeJur",
 *     version="0.1"
 * )
 */

class PublicacaoController extends RequestHandler
{
    private $authService;
    private $publicacaoService;
    private $apiManagerService;
    private $user;
    private $idConsumoWebService;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->authService       = new AuthService();
        $this->publicacaoService = new PublicacaoService();
        $this->apiManagerService = new ApiManagerService();

        $this->autentica();
    }

    private function autentica(){
        $username = $this->getParms('username');
        $password = $this->getParms('password');

        $this->user = $this->authService->authenticate($username, $password);
        $this->authService->verificaSeClientePodeConsumirPublicacao($this->user);
        $this->idConsumoWebService = $this->apiManagerService->salvarRequisicaoRecebida($this->user->getId(), $this->request);
    }

    /**
     * @OA\Post(
     *     path="/publicacoesNaoImportadas",
     *     tags={"Publicacoes"},
     *     summary="Obtem publicacoes nao importadas",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", example="seu_usuario"),
     *             @OA\Property(property="password", type="string", example="sua_senha")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lista de publicacoes nao importadas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Requisicao processada com sucesso"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="codigoPublicacao", type="integer"),
     *                     @OA\Property(property="diarioSigla", type="string"),
     *                     @OA\Property(property="dataPublicacao", type="string", format="date-time"),
     *                     @OA\Property(property="dataDisponibilizacao", type="string", format="date-time"),
     *                     @OA\Property(property="numeroProcesso", type="string"),
     *                     @OA\Property(property="orgaoDescricao", type="string"),
     *                     @OA\Property(property="varaDescricao", type="string"),
     *                     @OA\Property(property="textoPublicacao", type="string"),
     *                     @OA\Property(property="termoPesquisado", type="string"),
     *                     @OA\Property(property="documentos", type="array",
     *                          @OA\Items(type="string", example="linkDocumento1"),
     *                          @OA\Items(type="string", example="linkDocumento2"),
     *                          @OA\Items(type="string", example="linkDocumentoN")
     *                    )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", description="Not Found"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="400", description="Bad Request")
     * )
     */
    public function getPublicacoesNaoImportadas(){

        $listaPublicacoesNaoImportadas = $this->publicacaoService->getListaPublicacoesNaoImportadas($this->user);
        $this->apiManagerService->marcarListaIdsComoConsumidas($this->user->getId(), $this->idConsumoWebService, $listaPublicacoesNaoImportadas, 'codigoPublicacao');

        return new Response($listaPublicacoesNaoImportadas, '', HttpExceptionConstants::OK_CODE, true);
    }


    /**
     * @OA\Post(
     *     path="/publicacoesPorDia",
     *     tags={"Publicacoes"},
     *     summary="Obtem lista de publicacoes por dia",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", example="seu_usuario"),
     *             @OA\Property(property="password", type="string", example="sua_senha"),
     *             @OA\Property(property="data", type="date", example="Y-m-d"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Lista de publicacoes nao importadas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Requisicao processada com sucesso"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="codigoPublicacao", type="integer"),
     *                     @OA\Property(property="diarioSigla", type="string"),
     *                     @OA\Property(property="dataPublicacao", type="string", format="date-time"),
     *                     @OA\Property(property="dataDisponibilizacao", type="string", format="date-time"),
     *                     @OA\Property(property="numeroProcesso", type="string"),
     *                     @OA\Property(property="orgaoDescricao", type="string"),
     *                     @OA\Property(property="varaDescricao", type="string"),
     *                     @OA\Property(property="textoPublicacao", type="string"),
     *                     @OA\Property(property="termoPesquisado", type="string"),
     *                     @OA\Property(property="documentos", type="array",
     *                          @OA\Items(type="string", example="linkDocumento1"),
     *                          @OA\Items(type="string", example="linkDocumento2"),
     *                          @OA\Items(type="string", example="linkDocumentoN")
     *                    )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", description="Not Found"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="400", description="Bad Request")
     * )
     */
    public function getListaPublicacaoByData(){

        $dataFiltro = $this->getParms('data', [ValidationRules::notEmpty(), ValidationRules::validateDateTimeFormat('Y-m-d')]);

        $listaPublicacoesNaoImportadas = $this->publicacaoService->getListaPublicacaoByData($this->user->getId(), $dataFiltro);
        $this->apiManagerService->marcarListaIdsComoConsumidas($this->user->getId(), $this->idConsumoWebService, $listaPublicacoesNaoImportadas, 'codigoPublicacao');

        return new Response($listaPublicacoesNaoImportadas, '', HttpExceptionConstants::OK_CODE, true);
    }


    /**
     * @OA\Post(
     *     path="/publicacoesConfirmacao",
     *     tags={"Publicacoes"},
     *     summary="Confirma recebimento de publicacao",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"username", "password", "codigos"},
     *              @OA\Property(property="username", type="string", example="seu_usuario"),
     *              @OA\Property(property="password", type="string", example="sua_senha"),
     *              @OA\Property(property="codigos", type="array", example="[123456, 654321]", @OA\Items(type="integer"))
     *          )
     *      ),
     * @OA\Response(
     *     response="200",
     *     description="Lista de publicacoes nao importadas",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="status_code", type="integer", example=200),
     *         @OA\Property(
     *             property="response",
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Publicacoes Confirmadas"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="integer",
     *                     example=123456
     *                 )
     *             )
     *         )
     *     )
     * ),
     *     @OA\Response(response="404", description="Not Found"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="400", description="Bad Request")
     * )
     */
    public function confirmarRecebimentoPublicacao(){

        $listaIdPublicacoes = $this->getParms('codigos', [ValidationRules::notEmpty()]);

        $this->publicacaoService->confirmarRecebimentoPublicacoes($this->user->getId(), $listaIdPublicacoes);

        return new Response($listaIdPublicacoes, 'Publicações Confirmadas', HttpExceptionConstants::OK_CODE);
    }


    /**
     * @OA\Post(
     *     path="/publicacoesPorData",
     *     tags={"Publicacoes"},
     *     summary="Obtem quantitativo de publicacoes importadas e nao importadas por data",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", example="seu_usuario"),
     *             @OA\Property(property="password", type="string", example="sua_senha"),
     *             @OA\Property(property="data", type="date", example="Y-m-d"),
     *         )
     *     ),
     * @OA\Response(
     *     response="200",
     *     description="Estatisticas de publicacoes",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="totalPublicacoes", type="integer", example=2),
     *         @OA\Property(property="totalNaoImportadas", type="integer", example=2)
     *     )
     * ),
     *     @OA\Response(response="404", description="Not Found"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="400", description="Bad Request")
     * )
     */
    public function getQuantitativoPublicacoesImportadasENaoImportadasByData(){
        $dataFiltro = $this->getParms('data', [ValidationRules::notEmpty(), ValidationRules::validateDateTimeFormat('Y-m-d')]);

        $quantitativosPublicacoes = $this->publicacaoService->getQuantitativoPublicacoesImportadasENaoImportadasByData($this->user->getId(), $dataFiltro);

        return new Response($quantitativosPublicacoes, '', HttpExceptionConstants::OK_CODE, true);
    }

    public function __destruct()
    {
        $this->apiManagerService->marcarSucessoNaRequisicao($this->idConsumoWebService);
    }
}