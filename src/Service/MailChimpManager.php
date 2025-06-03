<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Service;

use DrewM\MailChimp\MailChimp;
use Symfony\Component\HttpFoundation\Response;
use ThreeBRS\SyliusMailChimpPlugin\Exception\MailChimpException;
use ThreeBRS\SyliusMailChimpPlugin\Exception\MailChimpInvalidErrorResponseException;
use ThreeBRS\SyliusMailChimpPlugin\Model\MailChimpLanguageEnum;
use ThreeBRS\SyliusMailChimpPlugin\Model\MailChimpSubscriptionStatusEnum;

class MailChimpManager implements MailChimpManagerInterface
{
    private ?MailChimp $mailChimp;

    public function __construct(MailChimpApiClientProvider $mailChimpApiClientProvider)
    {
        $this->mailChimp = $mailChimpApiClientProvider->getClient();
    }

    public function getContact(string $email, string $listId): ?array
    {
        assert($this->mailChimp instanceof MailChimp);
        assert(filter_var($email, \FILTER_VALIDATE_EMAIL));

        $result = $this->mailChimp->get("lists/$listId/members/" . MailChimp::subscriberHash($email));

        if (!$this->mailChimp->success()) {
            if (($this->mailChimp->getLastResponse()['headers']['http_code'] ?? null) === Response::HTTP_NOT_FOUND) {
                return null;
            }

            $this->throwMailChimpError($this->mailChimp->getLastResponse());
        }

        if ($result === false) {
            return null;
        }

        return $result;
    }

    public function isEmailSubscribedToList(string $email, string $listId): bool
    {
        $contact = $this->getContact($email, $listId);
        if ($contact === null) {
            return false;
        }

        return $contact['status'] === MailChimpSubscriptionStatusEnum::SUBSCRIBED;
    }

    /**
     * @param string $localeCode MailChimpLanguageEnum::SUPPORTED_LANGUAGES
     *
     * @return array<mixed>|null
     *
     * @throws MailChimpException
     */
    public function subscribeToList(string $email, string $listId, string $localeCode, bool $doubleOptInEnabled, array $data = []): ?array
    {
        assert($this->mailChimp instanceof MailChimp);
        assert(filter_var($email, \FILTER_VALIDATE_EMAIL));

        $localeCode = substr($localeCode, 0, 2);
        assert(in_array($localeCode, MailChimpLanguageEnum::SUPPORTED_LANGUAGES, true));
        $subscriberHash = MailChimp::subscriberHash($email);

        $options = [
            'email_address' => $email,
            'status' => $doubleOptInEnabled ? MailChimpSubscriptionStatusEnum::PENDING : MailChimpSubscriptionStatusEnum::SUBSCRIBED,
            'language' => $localeCode,
        ];

        if (count($data) > 0) {
            $options['merge_fields'] = $data;
        }

        if ($this->isEmailSubscribedToList($email, $listId)) {
            $result = $this->mailChimp->patch("lists/$listId/members/$subscriberHash", $options);
        } else {
            $result = $this->mailChimp->put("lists/$listId/members/$subscriberHash", $options);
        }

        if (!$this->mailChimp->success()) {
            $this->throwMailChimpError($this->mailChimp->getLastResponse());
        }

        return is_array($result) ? $result : null;
    }

    /**
     * @return array<mixed>|null
     */
    public function unsubscribeFromList(string $email, string $listId): ?array
    {
        assert($this->mailChimp instanceof MailChimp);
        assert(filter_var($email, \FILTER_VALIDATE_EMAIL));

        $subscriberHash = MailChimp::subscriberHash($email);

        $result = $this->mailChimp->patch(
            "lists/$listId/members/$subscriberHash",
            [
                'status' => MailChimpSubscriptionStatusEnum::UNSUBSCRIBED,
            ],
        );

        if (!$this->mailChimp->success()) {
            $this->throwMailChimpError($this->mailChimp->getLastResponse());
        }

        return is_array($result) ? $result : null;
    }

    /**
     * @return array<mixed>
     */
    public function getLists(): array
    {
        $mailChimp = $this->mailChimp;
        if ($mailChimp === null) {
            return [];
        }

        $lists = [];
        $count = 10;
        $page = 0;

        do {
            $result = $mailChimp->get('lists', [
                'offset' => $page * $count,
                'count' => $count,
            ]);

            if (!$mailChimp->success()) {
                $this->throwMailChimpError($mailChimp->getLastResponse());
            }

            ++$page;

            assert($result !== false);
            foreach ($result['lists'] as $list) {
                $lists[$list['id']] = $list['name'];
            }
        } while ($page * $count <= $result['total_items']);
        asort($lists);

        return $lists;
    }

    /**
     * @param array<mixed> $errorResponse
     */
    private function throwMailChimpError(array $errorResponse): void
    {
        if ($errorResponse['body'] === null) {
            throw new MailChimpInvalidErrorResponseException();
        }

        $errorArray = json_decode($errorResponse['body'], true);

        throw new MailChimpException(
            $errorArray['status'],
            $errorArray['detail'],
            $errorArray['type'],
            $errorArray['title'],
            $errorArray['errors'] ?? null,
            $errorArray['instance'],
        );
    }
}
