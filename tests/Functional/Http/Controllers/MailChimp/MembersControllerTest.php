<?php
/**
 * User: Gerwin
 * Date: 04/02/2019
 * Time: 2:24 AM
 */
declare(strict_types=1);

namespace Tests\App\Functional\Http\Controllers\MailChimp;

use Tests\App\TestCases\MailChimp\MemberTestCase;

class MembersControllerTest extends MemberTestCase
{
    /**
     * Test returns error response if list non existence.
     *
     * @return void
     */
    public function testIndexMemberNotFoundException() : void
    {
        $this->get('/mailchimp/lists/invalid-list-id/members');

        $this->assertListNotFoundResponse('invalid-list-id');
    }

    /**
     * Test returns successful response with all members data.
     *
     * @return void
     */
    public function testIndexMemberSuccessfully() : void
    {
        // Create Temp Member
        list($memberData, $member) = $this->createTemporaryMember();

        if (isset($member['member_id'])) {
            $this->createdMemberEmails[] = $member['email_address'];
        }

        $this->get('/mailchimp/lists/' . $this->mailChimpId . '/members');
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();
        self::assertEquals($this->mailChimpId, $content['list_id']);
        self::assertArrayHasKey('members', $content);
        self::assertArrayHasKey('list_id', $content);
    }

    /**
     * Test application returns error response with errors when member validation fails.
     *
     * @return void
     */
    public function testCreateMemberValidationFailed() : void
    {
        $this->post('/mailchimp/lists/' . $this->mailChimpId . '/members');

        $content = \json_decode($this->response->getContent(), true);

        $this->assertResponseStatus(400);
        self::assertArrayHasKey('message', $content);
        self::assertArrayHasKey('errors', $content);
        self::assertEquals('Invalid given data', $content['message']);

        foreach (\array_keys(static::$memberData) as $key) {
            if (\in_array($key, static::$notRequired, true)) {
                continue;
            }

            self::assertArrayHasKey($key, $content['errors']);
        }
    }

    /**
     * Test creates successfully member and returns it back with member id.
     *
     * @return void
     */
    public function testCreateMemberSuccessfully() : void
    {
        list($memberData, $content) = $this->createTemporaryMember();

        $this->assertResponseOk();
        $this->seeJson($memberData);
        self::assertArrayHasKey('member_id', $content);
        self::assertNotNull($content['member_id']);

        $this->createdMemberEmails[] = $content['email_address'];
    }

    /**
     * Test returns error response when member non existence.
     *
     * @return void
     */
    public function testShowMemberNotFoundException(): void
    {
        $this->get('/mailchimp/lists/' . $this->mailChimpId . '/members/invalid-member-id');

        $this->assertMemberNotFoundResponse('invalid-member-id', $this->mailChimpId);
    }

    /**
     * Test returns successful response with member data when requesting existing member.
     *
     * @return void
     */
    public function testShowMemberSuccessfully(): void
    {
        list($memberData, $member) = $this->createTemporaryMember();

        if (isset($member['member_id'])) {
            $this->createdMemberEmails[] = $member['email_address'];
        }

        $this->get('/mailchimp/lists/' . $this->mailChimpId . '/members/' . $member['member_id']);
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();
        foreach ($memberData as $key => $value) {
            if ($key == 'location') {
                self::assertArraySubset($value, $content[$key]);
            }
        }
    }

    /**
     * Test returns error response when member non existence.
     *
     * @return void
     */
    public function testUpdateMemberNotFoundException(): void
    {
        $this->put('/mailchimp/lists/' . $this->mailChimpId . '/members/invalid-member-id');

        $this->assertMemberNotFoundResponse('invalid-member-id', $this->mailChimpId);
    }

    /**
     * Test returns successfully response when updating existing member with updated data.
     *
     * @return void
     */
    public function testUpdateMemberSuccessfully(): void
    {
        // Create Temp Member
        list($memberData, $member) = $this->createTemporaryMember();

        if (isset($member['member_id'])) {
            $this->createdMemberEmails[] = $member['email_address']; // Store MailChimp member email address for cleaning purposes
        }

        $this->put('/mailchimp/lists/' . $this->mailChimpId . '/members/' . $member['member_id'], ['status' => 'subscribed']);
        $content = \json_decode($this->response->content(), true);

        $this->assertResponseOk();

        foreach (\array_keys($memberData) as $key) {
            self::assertArrayHasKey($key, $content);
            self::assertEquals('subscribed', $content['status']);
        }
    }

    /**
     * Test returns error response when member not found.
     *
     * @return void
     */
    public function testRemoveMemberNotFoundException() : void
    {
        $this->delete('/mailchimp/lists/' . $this->mailChimpId . '/members/invalid-member-id');

        $this->assertMemberNotFoundResponse('invalid-member-id', $this->mailChimpId);
    }

    /**
     * Test returns empty successful response when removing existing member.
     *
     * @return void
     */
    public function testRemoveMemberSuccessfully() : void
    {
        list($memberData, $content) = $this->createTemporaryMember();

        $this->delete('/mailchimp/lists/' . $this->mailChimpId . '/members/' . $content['member_id']);

        $this->assertResponseOk();
        self::assertEmpty(\json_decode($this->response->content(), true));
    }

}