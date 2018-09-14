<?php

namespace Enqueue\MessengerAdapter\Entity;

use Doctrine\ORM\Mapping as ORM;

trait UpdatedTrait
{
    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @return \DateTimeInterface|\DateTime|null
     */
    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    /**
     * @return self
     */
    public function updatedNow(): self
    {
        $this->updated = new \DateTime();

        return $this;
    }
}
