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

        $arguments = ['--provider' => $this->provider, '--force' => true];

        $this->call('vendor:publish', array_merge($arguments, ['--tag' => 'config']));

        $this->call('shield:generate', ['--resource' => collect($this->resources)->implode(', ')]);

        if ($this->confirm('Should publish migrations?')) {
            $this->call('vendor:publish', array_merge($arguments, ['--tag' => 'migrations']));
        }

        when($this->confirm('Run migrations?'), fn () => $this->call('migrate'));

        $this->call('vendor:publish', array_merge($arguments, ['--tag' => 'assets']));
        $this->call('vendor:publish', array_merge($arguments, ['--tag' => 'views']));

        $this->installHook();

        $this->info($name . ' installed successfully!');
    }

    protected function installHook(): void {}
}
