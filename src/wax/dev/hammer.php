<?php

declare(strict_types=1);

namespace wax\dev;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\block\VanillaBlocks;

class Hammer implements Listener {

    private ?Item $replacementItem = null;

    public function setReplacementItem(Item $replacementItem): void {
        $this->replacementItem = $replacementItem;
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $event->getItem();

        if ($item instanceof Tool && $item->getTier() === ToolTier::IRON()) {
            $event->setDrops([]);
            $this->breakInRadius($block, $player, $item);
            $this->reduceDurability($item, $player);
        }
    }

    private function breakInRadius(Block $block, Player $player, Item $item): void {
        $level = $block->getPosition()->getWorld();
        $pos = $block->getPosition();

        for ($x = -1; $x <= 1; $x++) {
            for ($y = -1; $y <= 1; $y++) {
                for ($z = -1; $z <= 1; $z++) {
                    $targetPos = $pos->add($x, $y, $z);
                    $targetBlock = $level->getBlockAt($targetPos->getX(), $targetPos->getY(), $targetPos->getZ());

                    if ($this->canBreak($targetBlock, $item)) {
                        $level->setBlock($targetPos, VanillaBlocks::AIR(), false, false);
                        foreach ($targetBlock->getDrops($item) as $drop) {
                            $level->dropItem($targetPos, $drop);
                        }
                    }
                }
            }
        }
    }

    private function canBreak(Block $block, Item $item): bool {
        $breakInfo = $block->getBreakInfo();
        return $breakInfo->getHardness() > 0 && $breakInfo->getBreakTime($item) < PHP_INT_MAX;
    }

    private function reduceDurability(Item $item, Player $player): void {
        if ($item->getDamage() < $item->getMaxDurability()) {
            $item->setDamage($item->getDamage() + 1);
            $player->getInventory()->setItemInHand($item);
        } else {
            if ($this->replacementItem !== null) {
                $player->getInventory()->setItemInHand($this->replacementItem);
            } else {
                $player->getInventory()->removeItem($item);
            }
        }
    }

    private function someMethodUsingMainInstance(): void {
        $mainInstance = Main::getInstance();
        $mainInstance->getLogger()->info("ez");
    }
}

