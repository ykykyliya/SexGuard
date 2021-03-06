<?php namespace sex\guard\listener;


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
use pocketmine\entity\Entity;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;


/**
 * @todo good listener should listen only one event.
 *       rewrite explode listener for more safety.
 */
class EntityGuard extends Manager implements Listener
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
	 *  _ _      _
	 * | (_)____| |_____ _ __   ___ _ __
	 * | | / __/   _/ _ \ '_ \ / _ \ '_/
	 * | | \__ \| ||  __/ | | |  __/ |
	 * |_|_|___/|___\___|_| |_|\___|_|
	 *
	 *
	 * @internal pvp flag.
	 *
	 * @param    EntityDamageEvent $event
	 *
	 * @priority        NORMAL
	 * @ignoreCancelled FALSE
	 */
	function onDamage( EntityDamageEvent $event )
	{
		if( $event->isCancelled() )
		{
			return;
		}
		
		$entity = $event->getEntity();
		
		if( $event instanceof EntityDamageByEntityEvent )
		{
			$damager = $event->getDamager();

			if( $entity instanceof Player )
			{
				if( $this->isFlagDenied($damager, 'pvp') )
				{
					$event->setCancelled();
				}
				
				if( $this->isFlagDenied($entity, 'pvp', true) )
				{
					$event->setCancelled();
				}
			}

			if( $this->isFlagDenied($damager, 'mob') )
			{
				$event->setCancelled();
			}
			
			if( $this->isFlagDenied($entity, 'mob') )
			{
				$event->setCancelled();
			}

			return;
		}

		if( $this->isFlagDenied($entity, 'damage') )
		{
			$event->setCancelled();
		}
	}


	/**
	 * @internal explode flag.
	 *
	 * @param    EntityExplodeEvent $event
	 *
	 * @priority        NORMAL
	 * @ignoreCancelled FALSE
	 */
	function onExplode( EntityExplodeEvent $event )
	{
		if( $event->isCancelled() )
		{
			return;
		}
		
		$entity = $event->getEntity();
		
		if( $this->isFlagDenied($entity, 'explode') )
		{
			$event->setBlockList([]);
		}
	}


	/**
	 * @internal teleport flag.
	 *
	 * @param    EntityTeleportEvent $event
	 *
	 * @priority        NORMAL
	 * @ignoreCancelled FALSE
	 */
	function onTeleport( EntityTeleportEvent $event )
	{
		if( $event->isCancelled() )
		{
			return;
		}
		
		$entity = $event->getEntity();
		
		if( $this->isFlagDenied($entity, 'teleport') )
		{
			$event->setCancelled();
		}
	}


	/**
	 * @param  Entity $entity
	 * @param  string $flag
	 * @param  bool   $ignore
	 *
	 * @return bool
	 */
	private function isFlagDenied( Entity $entity, string $flag, bool $ignore = FALSE ): bool
	{
		$api    = $this->api;
		$region = $api->getRegion($entity);
		
		if( !isset($region) )
		{
			return FALSE;
		}

		if( ($entity instanceof Player) and !$ignore )
		{
			$val = $api->getGroupValue($entity);
			
			if( in_array($flag, $val['ignored_flag']) )
			{
				if( !in_array($region->getRegionName(), $val['ignored_region']) )
				{
					return FALSE;
				}
			}
		}
		
		if( !$region->getFlagValue($flag) )
		{
			if( ($entity instanceof Player) and !$ignore )
			{
				$api->sendWarning($entity, $api->getValue('warn_flag_'.$flag));
			}
			
			return TRUE;
		}
		
		return FALSE;
	}
}