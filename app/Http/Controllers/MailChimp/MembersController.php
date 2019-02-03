<?php
/**
 * User: Gerwin
 * Date: 04/02/2019
 * Time: 12:08 AM
 */
declare(strict_types=1);

namespace App\Http\Controllers\MailChimp;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Database\Entities\MailChimp\MailChimpList;
use App\Database\Entities\MailChimp\MailChimpMember;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Mailchimp\Mailchimp;


class MembersController extends Controller
{
    /**
     * @var \Mailchimp\Mailchimp
     */
    private $mailChimp;

    /**
     * MemberController constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Mailchimp\Mailchimp $mailChimp
     */
    public function __construct(EntityManagerInterface $entityManager, Mailchimp $mailChimp)
    {
        parent::__construct($entityManager);
        $this->$mailChimp = $$mailChimp;
    }

    /**
     * @param string $listId
     * @return JsonResponse
     */
    public function index(string $listId) : JsonResponse
    {
        $list = $this->entityManager->getRepository(MailChimpList::class)->findOneBy([
           'mailChimpId' => $listId,
        ]);

        if (is_null($list)) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }

        try {
            $response = $this->mailChimp->get('lists/' . $listId . '/members');
        } catch (Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()]);
        }

        return $this->successfulResponse($response->toArray());
    }

    /**
     * @param Request $request
     * @param string $listId
     * @return JsonResponse
     */
    public function create(Request $request, string $listId) : JsonResponse
    {
        $member = new MailChimpMember($request->all());
        $validator = $this->getValidationFactory()->make($member->toMailChimpArray(), $member->getValidationRules());

        if ($validator->fails()) {
            return $this->errorResponse([
               'message' => 'Invalid given data',
               'errors' => $validator->errors()->toArray(),
            ]);
        }

        $list = $this->entityManager->getRepository(MailChimpList::class)->findOneBy([
            'mailChimpId' => $listId,
        ]);

        if (is_null($list)) {
            return $this->errorResponse(
                ['message' => \sprintf('MailChimpList[%s] not found', $listId)],
                404
            );
        }

        try {
            $this->mailChimp->post('list/' . $listId . '/members', $member->toMailChimpArray());
            $this->saveEntity($member->setListId($listId));
        } catch (Exception $exception) {
            return $this->errorResponse([
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * @param string $listId
     * @param string $memberId
     * @return JsonResponse
     */
    public function show(string $listId, string $memberId) : JsonResponse
    {
        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);

        if (is_null($member)) {
            return $this->errorResponse([
                'message' => \sprintf('MailChimpMember[member_id: %s, list_id: %s] Not Found',
                    $memberId,
                    $listId)
                ],
                404
            );
        }

        try {
            $response = $this->mailChimp->get('lists/' . $listId . '/members/'. \md5(\strtolower($member->getEmailAddress())));
        } catch (Exception $exception) {
            return $this->errorResponse([
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->successfulResponse($response->toArray());
    }

    /**
     * @param Request $request
     * @param string $listId
     * @param string $memberId
     * @return JsonResponse
     */
    public function update(Request $request, string $listId, string $memberId) : JsonResponse
    {
        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);

        if (is_null($member)) {
            return $this->errorResponse([
                'message' => \sprintf('MailChimpMember[member_id: %s, list_id: %s] Not Found',
                    $memberId,
                    $listId)
            ],
                404
            );
        }

        $member->fill($request->all());

        $validator = $this->getValidationFactory()->make($member->toMailChimpArray(), $member->getValidationRules());

        if ($validator->fails()) {
            return $this->errorResponse([
               'message' => 'Invalid given data',
               'error' => $validator->errors()->toArray(),
            ]);
        }

        try {
            $this->saveEntity($member);
            $this->mailChimp->patch('lists/' . $listId . '/members' . \md5(\strtolower(($member->getEmailAddress()))), $member->toMailChimpArray());
        } catch (Exception $exception) {
            return $this->errorResponse([
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->successfulResponse($member->toArray());
    }

    /**
     * @param string $listId
     * @param string $memberId
     * @return JsonResponse
     */
    public function remove(string $listId, string $memberId) : JsonResponse
    {
        $member = $this->entityManager->getRepository(MailChimpMember::class)->find($memberId);

        if (is_null($member)) {
            return $this->errorResponse([
                'message' => \sprintf('MailChimpMember[member_id: %s, list_id: %s] Not Found',
                    $memberId,
                    $listId)
            ],
                404
            );
        }

        try {
            $this->removeEntity($member);
            $this->mailChimp->delete('lists/' . $listId . '/members/' . \md5(\strtolower($member->getEmailAddress())));
        } catch (Exception $exception) {
            return $this->errorResponse([
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->successfulResponse([]);
    }

}