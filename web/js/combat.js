// this initiates the combat
function checkCombat(loc) {
    if (store.player.atk <= 1) {
        playerMessage("You can't attack.  You're too weak.  Find your strength!");
        return;
    }
    var monster = getMonsterAt(loc);

    // First we attack the monster
    var damage = getAttackResult(store.player.atk);

    // interesting thing here is that the damage can be negative thus healing the monster if it's def is > than
    // the attack.  initially a mistake, I realized it made for a rare added surprise game element.
    damage -= monster.enemyType.def;

    // if we kill it, then kill it and remove it
    if (damage >= monster.activeEnemy.hp) {
        var promise = killMonster(monster.enemyType, store.player.id);
        $.when(promise).done(() => {
            // make it real on the server first

            removeMonsterAt(loc);
            render();
            checkDone();
        });
    } else { // whelp, didn't kill it so it gets a counterattack.
        // it takes the damage but isn't killed
        monster.activeEnemy.hp -= damage;

        // counterattack
        var monsterDamage = getAttackResult(monster.enemyType.atk);

        // and here also if the player's defense is strong enough
        // the attack actually heals the player.  Get your defense high enough with potions
        // and you can also be healed by battling low level enemies
        monsterDamage -= store.player.def;

        // give the player the hit.  Store it in the DB
        var promise = changePlayerHP(-monsterDamage, store.player.id);
        $.when(promise).done(() => {
            // make it real on the server first
            playerMessage("You attack! Damage = " + damage + " But enemy hits for: " + monsterDamage);
            render();
            checkDead();
        });
    }
}

// you win!  Restart the game
function checkDone() {
    if (store.activeEnemies.length === 0) {
        alert("You defeated all the demons!  Your clan has regained honor!  Click to restart the game.");
        reset();
    }
}

// OUCH!
function changePlayerHP(hp, playerID) {
    var clonePlayer = { ...store.player };
    clonePlayer.hp += hp;
    return persistPlayer(clonePlayer, playerID);
}


// is the player dead?
// restart the game
function checkDead() {
    if (store.player.hp < 0) {
        alert("You have died!  Resetting game....");
        reset();
    }
}

function removeMonsterAt(loc) {
    var index = store.activeEnemies.findIndex((enemy) => (enemy.x == loc.x) && (enemy.y == loc.y));
    store.activeEnemies.splice(index, 1);
}


// just the attack damage
// total damage to the enemy/player is calculated elsewhere
function getAttackResult(atk) {
    var damage = 0;

    // you get one swing per attack value
    for (var i = 0; i < atk; i++) {
        damage += getRandom(6);
    }

    return damage;
}

// kill monster and get rewards from server
function killMonster(enemyType, playerID) {
    store.pause = true;
    // will update player and player items
    return $.post("api/rewardPlayerPOST.php", JSON.stringify( { enemyIDs: [enemyType.id], playerID: store.player.id }), function (result) {
        var dbResult = JSON.parse(result);

        var rewardedItem = selectItem(dbResult.rewardedItemID);

        $message ="You attack! And kill the " + enemyType.name + ". It gives you a " + rewardedItem.name + " and " + dbResult.rewardedXP + " XP!";
            
        alert($message);
        playerMessage($message);
        store.player = dbResult.player;
        store.playerItems = dbResult.playerItems ? dbResult.playerItems : [];
        store.pause = false;
        render();
    });
}