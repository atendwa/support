<?php

namespace Atendwa\Support;

class Command extends \Illuminate\Console\Command
{
    protected string $provider;

    /**
     * @var class-string[]
     */
    protected array $resources = [];

    public function handle(): void
    {
        $name = str(class_basename($this->provider))->before('ServiceProvider')->toString();

        $this->info('Installing ' . $name . '...');

        $this->call('vendor:publish', ['--provider' => $this->provider, '--tag' => 'config', '--force' => true]);

        $this->call('shield:generate', ['--resource' => collect($this->resources)->implode(', ')]);

        if ($this->confirm('Should publish migrations?')) {
            $this->call('vendor:publish', ['--provider' => $this->provider, '--tag' => 'migrations', '--force' => true]);
        }

        when($this->confirm('Run migrations?'), fn () => $this->call('migrate'));

        $this->info($name . ' installed successfully!');
    }
}
