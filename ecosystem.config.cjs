module.exports = {
  apps: [
    {
      name: "chetak-scheduler",
      script: "php",
      args: "artisan schedule:run",
      interpreter: "/usr/bin/php",
      cron_restart: "* * * * *",
      autorestart: false,
      watch: false,
      cwd: "/home/u230551100/domains/chetak.io/public_html/quotes",
    },
    {
      name: "chetak-queue",
      script: "php",
      args: "artisan queue:work --sleep=3 --tries=3 --timeout=90",
      interpreter: "/usr/bin/php",
      autorestart: true,
      watch: false,
      max_memory_restart: "256M",
      cwd: "/home/u230551100/domains/chetak.io/public_html/quotes",
    }
  ]
};
