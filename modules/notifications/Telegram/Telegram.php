<?php

namespace WHMCS\Module\Notification\Telegram;


use WHMCS\Config\Setting;
use WHMCS\Exception; 
use WHMCS\Module\Notification\DescriptionTrait;
use WHMCS\Module\Contracts\NotificationModuleInterface;
use WHMCS\Notification\Contracts\NotificationInterface;


class Telegram implements NotificationModuleInterface
{
	use DescriptionTrait;
	
    public function __construct()
    {
        $this->setDisplayName('Telegram')
            ->setLogoFileName('logo.png');
    }


	public function settings()
    {
        return [
            'botToken' => [
                'FriendlyName' => 'Token',
                'Type' => 'text',
                'Description' => 'Token of the Telegram Bot.',
                'Placeholder' => ' ',
            ],
            'botChatID' => [
                'FriendlyName' => 'chatID',
                'Type' => 'text',
                'Description' => 'ChatID of the user/channel.',
                'Placeholder' => ' ',
            ],
        ];
    }

	
	public function testConnection($settings)
    {
		$botToken = $settings['botToken'];
		$botChatID = $settings['botChatID'];
		
		$message = urlencode("Connected with WHMCS");
		$response = file_get_contents("https://api.telegram.org/bot".$botToken."/sendMessage?chat_id=".$botChatID."&text=".$message);

        if (!$response) { 
			throw new Exception('No response received from API');
		}
    }

	public function notificationSettings()
	{
		return [];
	}
	
	public function getDynamicField($fieldName, $settings)
	{
		return [];
	}


	public function sendNotification(NotificationInterface $notification, $moduleSettings, $notificationSettings)
    {
        $botToken = $moduleSettings['botToken'];
		$botChatID = $moduleSettings['botChatID'];
		
		$messageContent = "*". $notification->getTitle() ."*\n\n". $notification->getMessage() ."\n\n[Open »](". $notification->getUrl() .")";
		
		$message = urlencode($messageContent);
		$response = file_get_contents("https://api.telegram.org/bot".$botToken."/sendMessage?parse_mode=Markdown&chat_id=".$botChatID."&text=".$message);
		
        if (!$response) { 
			throw new Exception('No response received from API');
		}
    }
}
