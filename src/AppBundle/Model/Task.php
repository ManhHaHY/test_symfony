<?php

declare(strict_types=1);

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Task
{
    private $id;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="50")
     */
    private $title;
    /**
     * @Assert\Length(max="50")
     */
    private $description;

    /**
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    private $completed = false;

    /**
     * Task constructor.
     */
    public function __construct()
    {
        $this->id = uniqid();
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


    /**
     * @param bool $completed
     */
    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function getCompleted(): bool
    {
        return $this->completed;
    }
}
