<?php


namespace Consnet\Erporder\Cron;

class Status
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {

        $this->logger->addInfo("Cronjob Order Status status is executed.");
    }
}
