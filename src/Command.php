<?php

namespace Atendwa\Support;

class Command extends \Illuminate\Console\Command
{
    protected string $provider;

    /**
     * @var class-string[]
     */
    protected array $resources = [];

    private string $packageName;

    public function __construct()
    {
        parent::__construct();

        $this->packageName = str(class_basename($this->provider))->before('ServiceProvider')->toString();
    }

    public function handle(): void
    {
        $this->preInstall();

        $this->publishAssets();

        $this->finish();
    }

    protected function preInstall(): void
    {
        $this->info('Installing ' . $this->packageName . '...');
    }

    protected function finish(): void
    {
        $this->info($this->packageName . ' installed successfully!');
    }

    protected function publishAssets(): void
    {
        $arguments = ['--provider' => $this->provider, '--force' => true];

        $this->call('vendor:publish', array_merge($arguments, ['--tag' => 'config']));

        $this->call('shield:generate', ['--resource' => collect($this->resources)->implode(', ')]);

        if ($this->confirm('Should publish migrations?')) {
            $this->call('vendor:publish', array_merge($arguments, ['--tag' => 'migrations']));
        }

        when($this->confirm('Run migrations?'), fn () => $this->call('migrate'));

        $this->call('vendor:publish', array_merge($arguments, ['--tag' => 'assets']));
        $this->call('vendor:publish', array_merge($arguments, ['--tag' => 'views']));
    }
}
