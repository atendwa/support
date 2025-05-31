<?php

declare(strict_types=1);

namespace Atendwa\Support\Concerns\Services;

use Atendwa\Support\Concerns\Support\CanGenerateTempFiles;
use Google\Service\Directory\Resource\Users;
use Google\Service\Directory\User;
use Google_Client;
use Google_Service_Directory;
use Throwable;

trait InteractsWithGoogleWorkspace
{
    use CanGenerateTempFiles;

    protected ?Google_Service_Directory $connection = null;

    /**
     * @throws Throwable
     */
    public function __construct()
    {
        $this->connection();
    }

    /**
     * @throws Throwable
     */
    public function user(string $email): ?User
    {
        return $this->users()->get($email);
    }

    /**
     * @throws Throwable
     */
    public function thumbnail(string $email): ?string
    {
        return $this->user($email)?->thumbnailPhotoUrl;
    }

    /**
     * @throws Throwable
     */
    public function exists(string $email): bool
    {
        return $this->user($email) instanceof User;
    }

    /**
     * @throws Throwable
     */
    public function doesntExist(string $email): bool
    {
        return ! $this->exists($email);
    }

    /**
     * @throws Throwable
     */
    protected function users(): Users
    {
        $users = $this->connection()->users;

        throw_if(! $users instanceof Users, 'Invalid Google Workspace users service');

        return $users;
    }

    abstract protected function getAdminEmail(): string;

    protected function getAppName(): string
    {
        return asString(config('app.name'));
    }

    abstract protected function getAccessCredentials(): mixed;

    /**
     * @throws Throwable
     */
    protected function connection(): Google_Service_Directory
    {
        if (filled($this->connection)) {
            return $this->connection;
        }

        $credentials = $this->getAccessCredentials();
        throw_if(! is_array($credentials), 'Invalid Google workspace credentials');
        $credentials['private_key'] = str_replace('\\n', "\n", asString($credentials['private_key']));
        $this->generateFile(json_encode($credentials, JSON_PRETTY_PRINT));

        $googleClient = new Google_Client();
        $googleClient->setScopes(['https://www.googleapis.com/auth/admin.directory.user.readonly']);
        $googleClient->setApplicationName($this->getAppName());
        $googleClient->setSubject($this->getAdminEmail());
        $googleClient->setAuthConfig($this->filePath);

        $this->cleanFile();

        return $this->connection = new Google_Service_Directory($googleClient);
    }
}
