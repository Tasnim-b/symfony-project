<?php
// src/Entity/WorkoutProfile.php

namespace App\Entity;

use App\Repository\WorkoutProfileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkoutProfileRepository::class)]
class WorkoutProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "Veuillez sélectionner votre genre")]
    private ?string $gender = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez indiquer votre âge")]
    #[Assert\Range(
        min: 15,
        max: 80,
        notInRangeMessage: "L'âge doit être entre {{ min }} et {{ max }} ans"
    )]
    private ?int $age = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 30,
        max: 200,
        notInRangeMessage: "Le poids doit être entre {{ min }} et {{ max }} kg"
    )]
    private ?float $weight = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "Veuillez sélectionner votre niveau actuel")]
    private ?string $currentLevel = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Veuillez définir votre objectif principal")]
    private ?string $primaryGoal = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez indiquer le nombre de séances par semaine")]
    #[Assert\Range(
        min: 1,
        max: 7,
        notInRangeMessage: "Le nombre de séances doit être entre {{ min }} et {{ max }} par semaine"
    )]
    private ?int $sessionsPerWeek = null;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotBlank(message: "Veuillez sélectionner vos horaires préférés")]
    private array $preferredSchedule = [];

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez indiquer la durée souhaitée par séance")]
    #[Assert\Range(
        min: 20,
        max: 180,
        notInRangeMessage: "La durée doit être entre {{ min }} et {{ max }} minutes"
    )]
    private ?int $sessionDuration = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez sélectionner votre lieu d'entraînement")]
    private ?string $workoutEnvironment = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez indiquer votre équipement disponible")]
    private ?string $equipmentAvailable = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez indiquer votre niveau de motivation")]
    private ?string $motivationLevel = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: "Le niveau de stress doit être entre {{ min }} et {{ max }}"
    )]
    private ?int $stressLevel = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\NotBlank(message: "Veuillez indiquer votre qualité de sommeil")]
    private ?string $sleepQuality = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $physicalLimitations = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez indiquer votre consommation d'eau actuelle")]
    #[Assert\Range(
        min: 0.5,
        max: 10,
        notInRangeMessage: "La consommation d'eau doit être entre {{ min }}L et {{ max }}L par jour"
    )]
    private ?float $dailyWaterIntake = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $generatedWorkout = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->preferredSchedule = [];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getCurrentLevel(): ?string
    {
        return $this->currentLevel;
    }

    public function setCurrentLevel(string $currentLevel): static
    {
        $this->currentLevel = $currentLevel;

        return $this;
    }

    public function getPrimaryGoal(): ?string
    {
        return $this->primaryGoal;
    }

    public function setPrimaryGoal(string $primaryGoal): static
    {
        $this->primaryGoal = $primaryGoal;

        return $this;
    }

    public function getSessionsPerWeek(): ?int
    {
        return $this->sessionsPerWeek;
    }

    public function setSessionsPerWeek(int $sessionsPerWeek): static
    {
        $this->sessionsPerWeek = $sessionsPerWeek;

        return $this;
    }

    public function getPreferredSchedule(): array
    {
        return $this->preferredSchedule;
    }

    public function setPreferredSchedule(array $preferredSchedule): static
    {
        $this->preferredSchedule = $preferredSchedule;

        return $this;
    }

    public function getSessionDuration(): ?int
    {
        return $this->sessionDuration;
    }

    public function setSessionDuration(int $sessionDuration): static
    {
        $this->sessionDuration = $sessionDuration;

        return $this;
    }

    public function getWorkoutEnvironment(): ?string
    {
        return $this->workoutEnvironment;
    }

    public function setWorkoutEnvironment(?string $workoutEnvironment): static
    {
        $this->workoutEnvironment = $workoutEnvironment;

        return $this;
    }

    public function getEquipmentAvailable(): ?string
    {
        return $this->equipmentAvailable;
    }

    public function setEquipmentAvailable(?string $equipmentAvailable): static
    {
        $this->equipmentAvailable = $equipmentAvailable;

        return $this;
    }

    public function getMotivationLevel(): ?string
    {
        return $this->motivationLevel;
    }

    public function setMotivationLevel(?string $motivationLevel): static
    {
        $this->motivationLevel = $motivationLevel;

        return $this;
    }

    public function getStressLevel(): ?int
    {
        return $this->stressLevel;
    }

    public function setStressLevel(?int $stressLevel): static
    {
        $this->stressLevel = $stressLevel;

        return $this;
    }

    public function getSleepQuality(): ?string
    {
        return $this->sleepQuality;
    }

    public function setSleepQuality(?string $sleepQuality): static
    {
        $this->sleepQuality = $sleepQuality;

        return $this;
    }

    public function getPhysicalLimitations(): ?string
    {
        return $this->physicalLimitations;
    }

    public function setPhysicalLimitations(?string $physicalLimitations): static
    {
        $this->physicalLimitations = $physicalLimitations;

        return $this;
    }

    public function getDailyWaterIntake(): ?float
    {
        return $this->dailyWaterIntake;
    }

    public function setDailyWaterIntake(float $dailyWaterIntake): static
    {
        $this->dailyWaterIntake = $dailyWaterIntake;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getGeneratedWorkout(): ?string
    {
        return $this->generatedWorkout;
    }

    public function setGeneratedWorkout(?string $generatedWorkout): static
    {
        $this->generatedWorkout = $generatedWorkout;

        return $this;
    }
}
