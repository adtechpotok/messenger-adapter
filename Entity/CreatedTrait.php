<?php

namespace Enqueue\MessengerAdapter\Entity;

use Doctrine\ORM\Mapping as ORM;

trait CreatedTrait
{
    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @return \DateTimeInterface|\DateTime|null
     */
    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @return self
     */
    public function createdNow(): self
    {
        $this->created = new \DateTime();

        return $this;
    }
}
