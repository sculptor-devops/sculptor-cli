<?php

use Sculptor\Agent\Actions\Alarms\Factories\Conditions;
use Sculptor\Agent\Actions\Alarms\Factories\Methods;
use Sculptor\Agent\Actions\Alarms\Factories\Rearms;
use Sculptor\Agent\Actions\Alarms\Factories\Subjects;
use Sculptor\Agent\Actions\Alarms\Methods\Bash;
use Sculptor\Agent\Actions\Alarms\Methods\Webhook;
use Sculptor\Agent\Actions\Alarms\Rearms\Auto;
use Sculptor\Agent\Actions\Alarms\Rearms\Manual;
use Sculptor\Agent\Actions\Alarms\Subjects\Backup;
use Sculptor\Agent\Actions\Alarms\Subjects\Certificate;
use Sculptor\Agent\Actions\Alarms\Subjects\Monitor;
use Sculptor\Agent\Actions\Alarms\Subjects\ResponseStatus;
use Sculptor\Agent\Actions\Alarms\Subjects\ResponseTime;
use Sculptor\Agent\Actions\Alarms\Subjects\Security;
use Sculptor\Agent\Actions\Alarms\Conditions\Compare;
use Sculptor\Agent\Actions\Alarms\Conditions\Delta;

use Sculptor\Agent\Actions\Backups\Factories\Archives;
use Sculptor\Agent\Actions\Backups\Factories\Compressions;
use Sculptor\Agent\Actions\Backups\Factories\Dumpers;
use Sculptor\Agent\Actions\Backups\Factories\Rotations;
use Sculptor\Agent\Actions\Backups\Factories\Strategies;
use Sculptor\Agent\Actions\Backups\Rotations\Count;
use Sculptor\Agent\Actions\Backups\Rotations\Days;

use Sculptor\Agent\Actions\Backups\Strategies\Blueprint;
use Sculptor\Agent\Actions\Backups\Strategies\Database;
use Sculptor\Agent\Actions\Backups\Strategies\Domain;

use Sculptor\Agent\Actions\Daemons\Services\Fail2ban;
use Sculptor\Agent\Actions\Daemons\Services\Nginx;
use Sculptor\Agent\Actions\Daemons\Services\Ssh;
use Sculptor\Agent\Actions\Daemons\Services\Supervisor;
use Sculptor\Agent\Actions\Daemons\Services\Redis;
use Sculptor\Agent\Actions\Daemons\Services\MySql;

use Sculptor\Agent\Actions\Backups\Compression\Zip;
use Sculptor\Agent\Actions\Backups\Archives\Local;
use Sculptor\Agent\Actions\Backups\Archives\S3;
use Sculptor\Agent\Actions\Backups\Archives\Dropbox;

use Sculptor\Agent\Actions\Backups\Dumpers\MySql as MySqlDump;
use Sculptor\Agent\Actions\Domains\Certificates\Custom;
use Sculptor\Agent\Actions\Domains\Certificates\LetsEncrypt;
use Sculptor\Agent\Actions\Domains\Certificates\SelfSigned;
use Sculptor\Agent\Actions\Domains\Stages\Certificates;
use Sculptor\Agent\Actions\Support\Daemons;
use Sculptor\Agent\Monitors\Collector;
use Sculptor\Agent\Monitors\System\Cpu;
use Sculptor\Agent\Monitors\System\Disk;
use Sculptor\Agent\Monitors\System\Io;
use Sculptor\Agent\Monitors\System\Memory;
use Sculptor\Agent\Monitors\System\Uptime;

return [
    'factories' => [
        Collector::class => 'sculptor.monitors',
        Daemons::class => 'sculptor.services',
        Archives::class => 'sculptor.backup.archives',
        Compressions::class => 'sculptor.backup.compressions',
        Strategies::class => 'sculptor.backup.strategies',
        Rotations::class => 'sculptor.backup.rotations',
        Dumpers::class => 'sculptor.backup.dumpers',
        Certificates::class => 'sculptor.domains.certificates',
        Conditions::class => 'sculptor.alarms.conditions',
        Subjects::class => 'sculptor.alarms.subjects',
        Methods::class => 'sculptor.alarms.methods',
        Rearms::class => 'sculptor.alarms.rearms'
    ],

    'domains' => [
        'certificates' => [
            LetsEncrypt::class,
            Custom::class,
            SelfSigned::class
        ]
    ],

    'services' => [
        MySql::class,
        Nginx::class,
        Redis::class,
        Supervisor::class,
        Ssh::class,
        Fail2ban::class
    ],

    'monitors' => [
        Cpu::class,
        Disk::class,
        Io::class,
        Memory::class,
        Uptime::class
    ],

    'backup' => [
        'strategies' => [
            Blueprint::class,
            Database::class,
            Domain::class
        ],

        'archives' => [
            Local::class,
            S3::class,
            Dropbox::class
        ],

        'rotations' => [
            Days::class,
            Count::class
        ],

        'compressions' => [
            Zip::class
        ],

        'dumpers' => [
            MySqlDump::class
        ]
    ],

    'database' => [
        'drivers' => [
            'mysql' => [
                'driver' => env('MYSQL_DATABASE_DRIVER', 'mysql'),
                'host' => env('MYSQL_DATABASE_HOST', '127.0.0.1'),
                'port' => env('MYSQL_DATABASE_PORT', '3306'),
                'database' => env('MYSQL_DATABASE_NAME', 'mysql'),
                'username' => env('MYSQL_DATABASE_USERNAME', 'root'),
                'password' => 'password'
            ]
        ]
    ],

    'alarms' => [
        'conditions' => [
            Compare::class,
            Delta::class
        ],
        'methods' => [
            Bash::class,
            Webhook::class
        ],
        'rearms' => [
            Auto::class,
            Manual::class
        ],
        'subjects' => [
            Backup::class,
            Monitor::class,
            ResponseStatus::class,
            ResponseTime::class,
            Security::class,
            Certificate::class
        ],
    ]
];
