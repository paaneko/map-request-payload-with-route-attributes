<?php

declare(strict_types=1);

namespace App\Request;

use App\Enum\ProjectTypeEnum;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ExampleRequest
{
    #[Groups('read')]
    private ?string $project = null;

    #[Groups(['read', 'write'])]
    #[Assert\NotNull]
    private ?ProjectTypeEnum $projectType = null;

    #[Groups(['read', 'write'])]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $projectName = null;

    #[Groups(['read', 'write'])]
    #[Assert\NotNull]
    #[Assert\Range(min: 100, max: 1000)]
    private ?int $projectPrice = null;

    public function getProject(): ?string
    {
        return $this->project;
    }

    public function setProject(?string $project): ExampleRequest
    {
        $this->project = $project;
        return $this;
    }

    public function getProjectType(): ?ProjectTypeEnum
    {
        return $this->projectType;
    }

    public function setProjectType(?ProjectTypeEnum $projectType): ExampleRequest
    {
        $this->projectType = $projectType;
        return $this;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }

    public function setProjectName(?string $projectName): ExampleRequest
    {
        $this->projectName = $projectName;
        return $this;
    }

    public function getProjectPrice(): ?int
    {
        return $this->projectPrice;
    }

    public function setProjectPrice(?int $projectPrice): ExampleRequest
    {
        $this->projectPrice = $projectPrice;
        return $this;
    }
}
