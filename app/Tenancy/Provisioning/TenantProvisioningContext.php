<?php

namespace App\Tenancy\Provisioning;

use App\Enums\Landlord\ProvisioningStep;
use DateTimeImmutable;

class TenantProvisioningContext
{
    private ?string $tenantId = null;

    private ?string $jobId = null;

    private int $attempt = 1;

    private ?ProvisioningStep $currentStep = null;

    private ?DateTimeImmutable $startedAt = null;

    /**
     * @var array<string, float>
     */
    private array $stepTimings = [];

    public function start(string $tenantId, ?string $jobId = null, int $attempt = 1): void
    {
        $this->tenantId = $tenantId;
        $this->jobId = $jobId;
        $this->attempt = $attempt;
        $this->currentStep = null;
        $this->startedAt = new DateTimeImmutable;
        $this->stepTimings = [];
    }

    public function setStep(ProvisioningStep $step): void
    {
        $this->currentStep = $step;
    }

    public function getStep(): ?ProvisioningStep
    {
        return $this->currentStep;
    }

    public function recordStepTiming(ProvisioningStep $step, float $durationMs): void
    {
        $this->stepTimings[$step->value] = $durationMs;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function getJobId(): ?string
    {
        return $this->jobId;
    }

    public function getAttempt(): int
    {
        return $this->attempt;
    }

    public function getStartedAt(): ?DateTimeImmutable
    {
        return $this->startedAt;
    }

    /**
     * @return array<string, float>
     */
    public function getStepTimings(): array
    {
        return $this->stepTimings;
    }

    public function getTotalLatencyMs(): float
    {
        if ($this->startedAt === null) {
            return 0.0;
        }

        $now = microtime(true);
        $start = (float) $this->startedAt->format('U.u');

        return round(($now - $start) * 1000, 2);
    }

    public function reset(): void
    {
        $this->tenantId = null;
        $this->jobId = null;
        $this->attempt = 1;
        $this->currentStep = null;
        $this->startedAt = null;
        $this->stepTimings = [];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenantId,
            'job_id' => $this->jobId,
            'attempt' => $this->attempt,
            'current_step' => $this->currentStep?->value,
            'started_at' => $this->startedAt?->format(DateTimeImmutable::ATOM),
            'step_timings' => $this->stepTimings,
            'total_latency_ms' => $this->getTotalLatencyMs(),
        ];
    }
}
