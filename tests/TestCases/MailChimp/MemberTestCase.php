<?php
/**
 * User: Gerwin
 * Date: 04/02/2019
 * Time: 2:00 AM
 */
declare(strict_types=1);

namespace Tests\App\TestCases\MailChimp;

use App\Database\Entities\MailChimp\MailChimpMember;
use Illuminate\Http\JsonResponse;
use Mailchimp\Mailchimp;
use Mockery;
use Mockery\MockInterface;
use Tests\App\TestCases\WithDatabaseTestCase;

abstract class MemberTestCase extends WithDatabaseTestCase
{
    /**
     *
     */
    protected const MAILCHIMP_EXCEPTION_MESSAGE = 'MailChimp Exception';

    /**
     * @var array
     */
    protected $createdMemberEmails = [];

    /**
     * @var string
     */
    protected $mailChimpId = '9433f0c06';

    /**
     * @var array
     */
    protected static $memberData = [
        'list_id' => '9433f0c06',
        'email_address' => 'iamgerwin@aim.com',
        'email_type' => 'html',
        'status' => 'subscribed',
        'vip' => true,
        'language' => 'en',
        'location' => [
            'latitude' => 14.529410,
            'longitude' => 121.071022
        ],
        'ip_signup' => '192.168.0.1',
        'timestamp_signup' => '2019-02-04 02:33:33',
        'interest' => [
            'computer',
            'learn',
            'read',
            'travel',
        ],
        'tags' => [
            'tag_1',
            'tag_2',
            'tag_3',
        ],
        'merge_fields' => [
            'FNAME' => 'John Gerwin',
            'LNAME' => 'De las Alas',
            'ADDRESS' => 'Makati',
            'PHONE' => '9392233111',
        ],
    ];

    protected static $notRequired = [
        'email_type',
        'interests',
        'language',
        'vip',
        'location',
        'ip_signup',
        'timestamp_signup',
        'tags',
        'merge_fields',
    ];

    /**
     * Create MailChimp member into database.
     *
     * @param array $data
     *
     * @return \App\Database\Entities\MailChimp\MailChimpMember
     */
    protected function createMember(array $data) : MailChimpMember
    {
        // Create a member for list
        $member = new MailChimpMember($data);
        $member->setListId($this->mailChimpId);

        $this->entityManager->persist($member);
        $this->entityManager->flush();

        return $member;
    }

    /**
     * Create a temporary member for testing
     *
     * @return array
     */
    protected function createTemporaryMember() : array
    {
        $this->createTemporaryList();

        $memberData = static::$memberData;
        $memberData['email_address'] = uniqid() . '@yahoo.com';

        $this->post('/mailchimp/lists/' . $this->mailChimpId . '/members', $memberData);
        $content = \json_decode($this->response->getContent(), true);

        return [$memberData, $content];
    }

    /**
     * Create A Temporary List Data For Testing Member
     *
     * @return void
     */
    protected function createTemporaryList() : void
    {
        $listData = static::$listData;
        $listData['mail_chimp_id'] = $this->mailChimpId;
        $this->createList($listData);
    }

    /**
     * Returns mock of MailChimp to trow exception when requesting their API.
     *
     * @param string $method
     * @return \Mockery\MockInterface
     * @SuppressWarnings(PHPMD.StaticAccess) Mockery requires static access to mock()
     */
    protected function mockMailChimpForException(string $method) : MockInterface
    {
        $mailChimp = Mockery::mock(Mailchimp::class);

        $mailChimp
            ->shouldReceive($method)
            ->once()
            ->withArgs(function (string $method, ?array $options = null) {
                return !empty($method) && (null === $options || \is_array($options));
            })
            ->andThrow(new \Exception(self::MAILCHIMP_EXCEPTION_MESSAGE));

        return $mailChimp;
    }

    /**
     * Assert error response when MailChimp exception is thrown.
     *
     * @param \Illuminate\Http\JsonResponse $response
     * @return void
     */
    protected function assertMailChimpExceptionResponse(JsonResponse $response) : void
    {
        $content = \json_decode($response->content(), true);

        self::assertEquals(400, $response->getStatusCode());
        self::assertArrayHasKey('message', $content);
        self::assertEquals(self::MAILCHIMP_EXCEPTION_MESSAGE, $content['message']);
    }

    /**
     * Asserts error response when member not found.
     *
     * @param string $memberId
     * @param string $listId
     * @return void
     */
    protected function assertMemberNotFoundResponse(string $memberId, string $listId) : void
    {
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseStatus(404);
        self::assertArrayHasKey('message', $content);
        self::assertEquals(\sprintf('MailChimpMember[member_id: %s, list_id: %s] not found', $memberId, $listId), $content['message']);
    }

    /**
     * Call MailChimp to delete members created during test.
     *
     * @return void
     */
    public function tearDown() : void
    {
        /** @var Mailchimp $mailChimp */
        $mailChimp = $this->app->make(Mailchimp::class);

        foreach ($this->createdMemberEmails as $memberEmail) {
            $mailChimp->delete('lists/' . $this->mailChimpId . '/members/' . \md5(\strtolower($memberEmail)));
        }

        parent::tearDown();
    }
}