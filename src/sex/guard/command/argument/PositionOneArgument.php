<?php namespace sex\guard\command\argument;


/**
 *  _    _       _                          _  ____
 * | |  | |_ __ (_)_    _____ _ ______ __ _| |/ ___\_ _______      __
 * | |  | | '_ \| | \  / / _ \ '_/ __// _' | | /   | '_/ _ \ \    / /
 * | |__| | | | | |\ \/ /  __/ | \__ \ (_) | | \___| ||  __/\ \/\/ /
 *  \____/|_| |_|_| \__/ \___|_| /___/\__,_|_|\____/_| \___/ \_/\_/
 *
 * @author sex_KAMAZ
 * @link   http://universalcrew.ru
 *
 */
use sex\guard\Manager;

use pocketmine\Player;
use pocketmine\level\Position;


/**
 * @todo nothing.
 */
class PositionOneArgument extends Manager
{
	/**
	 * @var Manager
	 */
	private $api;


	/**
	 * @param Manager $api
	 */
	function __construct( Manager $api )
	{
		$this->api = $api;
	}


	/**
	 *                                          _
	 *   __ _ _ ____ _ _   _ _ __ _   ___ _ ___| |_
	 *  / _' | '_/ _' | | | | '  ' \ / _ \ '_ \   _\
	 * | (_) | || (_) | |_| | || || |  __/ | | | |_
	 *  \__,_|_| \__, |\___/|_||_||_|\___|_| |_|\__\
	 *           /___/
	 *
	 * @param  Player   $sender
	 * @param  string[] $args
	 *
	 * @return bool
	 */
	function execute( Player $sender, array $args ): bool
	{
		$nick = strtolower($sender->getName());
		$api  = $this->api;
		$pos  = new Position(
			$sender->getFloorX(),
			$sender->getFloorY(),
			$sender->getFloorZ(),
			$sender->getLevel()
		);

		$region = $api->getRegion($pos);
		
		if( $region !== NULL and !$sender->hasPermission('sexguard.all') )
		{
			if( $region->getOwner() != $nick )
			{
				$sender->sendMessage($api->getValue('rg_override'));
				return FALSE;
			}
		}
		
		if( isset($api->position[1][$nick]) )
		{
			unset($api->position[1][$nick]);
		}
		
		$api->position[0][$nick] = $pos;
		
		$sender->sendMessage($api->getValue('pos_1_set'));
		return TRUE;
	}
}