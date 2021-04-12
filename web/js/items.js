
function checkItem(loc) {
    var item = getItemAt(loc);
    var promise = addPlayerItem(item, store.player.id);

    // isn't real until the server says it's real
    $.when(promise).done(() => {
        playerMessage("You found an " + item.name);
        removeItemFromMap(loc);
        render();
    });


}

function applyAreaEffect(key,value) {
  store.activeEnemies.forEach((enemy) => {
    enemy[key]+= value;
    var enemyType = store.enemies.find((e) => e.type === enemy.type);
    if (enemy.hp <=0){
        var promise = killMonster(enemyType, store.player.id);
        $.when(promise).done(() => {
            // make it real on the server first
    
            removeMonsterAt({x:enemy.x, y:enemy.y});
            render();
            checkDone();
        });
    }
  });
}

function useItem(slot) {
    if (store.playerItems.length < slot) {
        console.log("no item for slot " + slot);
        return;
    }
    var playerItem = store.playerItems[slot - 1];
    var item = selectItem(playerItem.itemID);
    var message = '';
    if (item.playerEffect) {
        var clonePlayer = { ...store.player };
         effectKeys = Object.keys(item.playerEffect);
        if (effectKeys) {
            effectKeys.forEach((key) => {
                clonePlayer[key] += item.playerEffect[key];
                // of course in here would be neat to put in all kinds of effects like 
                // saying the player was stronger, or weaker etc. 
                message ="You drank the potion!  " + key + " +" + item.playerEffect[key];

            });
        }
    } else if (item.enemyEffect) {
        if (item.enemyEffect.type=="area"){
            effectKeys = Object.keys(item.enemyEffect);
            if (effectKeys) {
                effectKeys.forEach((key) => {
                    if (key !== "type"){
                        applyAreaEffect(key,item.enemyEffect[key]);
                        message ="BOOM!";
                    }
                });
            }
          
        }
    } 



    // clone items to save so that we don't 
    // change the client if the save fails.
    var cloneItems = [...store.playerItems];
    var clonedItem = cloneItems[slot - 1];
    if (clonedItem.qty > 1) {
        clonedItem.qty--;
    } else {
        cloneItems.splice(slot - 1, 1);
    }

    var persist = persistGameData({ player: clonePlayer, playerItems: cloneItems });
    $.when(persist).done(() => {
        playerMessage(message);
        render();
    });



}

function addPlayerItem(item, playerID) {
    var cloneItems = [...store.playerItems];
    var existing = cloneItems.find((cloneItem) => cloneItem.itemID == item.id);
    if (existing) {
        existing.qty++;
    } else {
        var playerItemObject = { playerID: playerID, itemID: item.id, qty: 1 };
        cloneItems.push(playerItemObject);
    }
    return persistPlayerItems(cloneItems, store.player.id);
}

function removeItemFromMap(loc) {
    var index = store.mapItems.findIndex((item) => (item.x == loc.x) && (item.y == loc.y));
    store.mapItems.splice(index, 1);
}

// only update if the persist was successful
// don't want to have a player that thinks they have an item
// when the backend doesn't agree
function persistPlayerItems(playerItems, playerID) {

    return $.post("api/playerItemPOST.php",JSON.stringify( { playerItems, playerID }), function (result) {
        var dbPlayerItems = JSON.parse(result);
        store.playerItems = dbPlayerItems;

    });
}