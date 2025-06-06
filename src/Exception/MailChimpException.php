<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Exception;

class MailChimpException extends \Exception
{
    /**
     * The HTTP status code (RFC2616, Section 6) generated by the origin server
     * for this occurrence of the problem.
     *
     * @var int
     */
    private $status;

    /**
     * A human-readable explanation specific to this occurrence of the problem.
     *
     * @var string
     */
    private $detail;

    /**
     * An absolute URI that identifies the problem type. When dereferenced,
     * it should provide human-readable documentation for the problem type.
     *
     * @var string
     */
    private $type;

    /**
     * A short, human-readable summary of the problem type.
     * It shouldn’t change based on the occurrence of the problem,
     * except for purposes of localization.
     *
     * @var string
     */
    private $title;

    /**
     * For field-specific details, see the 'errors' array.
     *
     * @var array<mixed>
     */
    private $errors;

    /**
     * A string that identifies this specific occurrence of the problem.
     *
     * @var string|null
     */
    private $instance;

    /**
     * @see http://developer.mailchimp.com/documentation/mailchimp/guides/get-started-with-mailchimp-api-3/#errors
     *
     * @param array<mixed> $errors
     */
    public function __construct(
        int $status,
        string $detail,
        string $type,
        string $title,
        ?array $errors = [],
        ?string $instance = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($detail, $status, $previous);
        $this->status = $status;
        $this->detail = $detail;
        $this->type = $type;
        $this->title = $title;
        $this->errors = $errors ?? [];
        $this->instance = $instance;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param array<mixed> $errors
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    public function setInstance(string $instance): self
    {
        $this->instance = $instance;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getInstance(): ?string
    {
        return $this->instance;
    }

    /**
     * @return array<mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
