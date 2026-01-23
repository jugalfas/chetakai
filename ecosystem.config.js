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
    }
  ]
};
