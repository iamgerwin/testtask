<?php
/**
 * User: Gerwin
 * Date: 04/02/2019
 * Time: 2:35 AM
 */
declare(strict_types=1);

namespace Tests\App\Unit\Http\Controllers\MailChimp;

use App\Http\Controllers\MailChimp\MembersController;
use Tests\App\TestCases\MailChimp\MemberTestCase;

class MembersControllerTest extends MemberTestCase
{
    /**
     * Test controller returns error response when exception is thrown during get all members.
     *
     * @return void
     */
    public function testShowAllMembersMailChimpException() : void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('get'));
        $member = $this->createMember(static::$memberData);

        if (is_null($member->getId())) {
            self::markTestSkipped('Unable to get the member, no id provided');

            return;
        }

        list($memberData, $member) = $this->createTemporaryMember();
        if (isset($member['member_id'])) {
            $this->createdMemberEmails[] = $member['email_address'];
        }

        $this->assertMailChimpExceptionResponse($controller->showAll($this->mailChimpId));
    }

    /**
     * Test controller returns error response when exception is thrown during get specific member.
     *
     * @return void
     */
    public function testShowSpecificMemberMailChimpException() : void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('get'));
        $member = $this->createMember(static::$memberData);

        if (is_null($member->getId())) {
            self::markTestSkipped('Unable to get the member, no id provide');

            return;
        }

        $this->assertMailChimpExceptionResponse($controller->show($this->mailChimpId, $member->getId()));
    }

    /**
     * Test controller returns error response when exception is thrown during post/create request.
     *
     * @return void
     */
    public function testCreateMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('post'));

        list($memberData, $member) = $this->createTemporaryMember();
        if (isset($member['member_id'])) {
            $this->createdMemberEmails[] = $member['email_address'];
        }

        $this->assertMailChimpExceptionResponse($controller->create($this->getRequest(static::$memberData), $this->mailChimpId));
    }

    /**
     * Test controller returns error response when exception is thrown during patch request.
     *
     * @return void
     */
    public function testUpdateMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('patch'));
        $member = $this->createMember(static::$memberData);

        if (is_null($member->getId())) {
            self::markTestSkipped('Unable to update, no id provided for member');

            return;
        }

        $this->assertMailChimpExceptionResponse($controller->update($this->getRequest(), $this->mailChimpId, $member->getId()));
    }

    /**
     * Test controller returns error response when exception is thrown during delete request.
     *
     * @return void
     */
    public function testRemoveMemberMailChimpException(): void
    {
        /** @noinspection PhpParamsInspection Mock given on purpose */
        $controller = new MembersController($this->entityManager, $this->mockMailChimpForException('delete'));
        $member = $this->createMember(static::$memberData);

        if (is_null($member->getId())) {
            self::markTestSkipped('Unable to remove, no id provided for member');

            return;
        }

        $this->assertMailChimpExceptionResponse($controller->remove($this->mailChimpId, $member->getId()));
    }

}