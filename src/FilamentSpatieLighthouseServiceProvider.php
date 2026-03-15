<?php

namespace FilamentSpatieLighthouse;

use FilamentSpatieLighthouse\Console\Kernel as LighthouseKernel;
use FilamentSpatieLighthouse\Console\ListLighthouseAuditsCommand;
use FilamentSpatieLighthouse\Console\RunLighthouseAuditCommand;
use FilamentSpatieLighthouse\Console\ScheduleLighthouseAuditCommand;
use FilamentSpatieLighthouse\Events\AuditEndedEvent;
use FilamentSpatieLighthouse\Events\AuditFailedEvent;
use FilamentSpatieLighthouse\Listeners\SendAuditCompletedNotifications;
use FilamentSpatieLighthouse\Listeners\SendAuditFailedNotifications;
use FilamentSpatieLighthouse\ResultStores\CacheResultStore;
use FilamentSpatieLighthouse\ResultStores\DatabaseResultStore;
use FilamentSpatieLighthouse\ResultStores\ResultStore;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentSpatieLighthouseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-spatie-lighthouse')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasMigrations(['create_lighthouse_audit_results_table'])
            ->hasRoutes('web')
            ->hasCommands(
                RunLighthouseAuditCommand::class,
                ListLighthouseAuditsCommand::class,
                ScheduleLighthouseAuditCommand::class,
            );
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ResultStore::class, function ($app) {
            $storeType = config('filament-spatie-lighthouse.result_store', 'database');

            return match ($storeType) {
                'database' => new DatabaseResultStore(),
                'cache' => new CacheResultStore(
                    cacheTtl: config('filament-spatie-lighthouse.cache_ttl', 86400)
                ),
                default => new DatabaseResultStore(),
            };
        });
    }

    public function packageBooted(): void
    {
        // Register event listeners if notifications are enabled
        if (
            config('filament-spatie-lighthouse.notifications.email.enabled') ||
            config('filament-spatie-lighthouse.notifications.slack.enabled')
        ) {
            Event::listen(AuditFailedEvent::class, SendAuditFailedNotifications::class);
            Event::listen(AuditEndedEvent::class, SendAuditCompletedNotifications::class);
        }

        // Hook scheduled audits into the application scheduler
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            (new LighthouseKernel)->schedule($schedule);
        });
    }
}
