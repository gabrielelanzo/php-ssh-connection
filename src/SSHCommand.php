<?php

namespace DivineOmega\SSHConnection;

use phpseclib3\Net\SSH2;

class SSHCommand
{
    public const EXECUTION_TIMEOUT_SECONDS = 30;

    private SSH2 $ssh;
    private string $command;
    private string|false $output;
    private string $error;

    public function __construct(SSH2 $ssh, string $command)
    {
        $this->ssh = $ssh;
        $this->command = $command;

        $this->execute();
    }

    private function execute(): void
    {
        $this->ssh->enableQuietMode();
        $this->ssh->setTimeout(self::EXECUTION_TIMEOUT_SECONDS);

        $this->output = $this->ssh->exec($this->command);
        $this->error = $this->ssh->getStdError();
    }

    public function getRawOutput(): string
    {
        if ($this->output === false) {
            throw new \RuntimeException('SSH command execution failed.');
        }

        return $this->output;
    }

    public function getRawError(): string
    {
        return $this->error;
    }

    public function getOutput(): string
    {
        return trim($this->getRawOutput());
    }

    public function getError(): string
    {
        return trim($this->getRawError());
    }
}