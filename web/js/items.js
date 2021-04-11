
function checkItem(loc) {
    var item = getItemAt(loc);
    var promise = addPlayerItem(item, store.player.id);

        // isn't real until the server says it's real
        $.when(promise).done(() => {
            playerMessage("You found an "+ item.name);
            removeItemFromMap(loc);
            render();
        });
  
    
}

function useItem(slot) {
    if (store.playerItems.length  < slot){
        console.log("no item for slot " + slot);
        return;
    }
    var playerItem = store.playerItems[slot -1];
    var item = selectItem(playerItem.itemID);
    if (item.playerEffect) {
        var clonePlayer = {...store.player};
        var effectKeys = Object.keys(item.playerEffect);
        if (effectKeys) {
            effectKeys.forEach( (key) => { clonePlayer[key]+= item.playerEffect[key];
                // of course in here would be neat to put in all kinds of effects like 
                // saying the player was stronger, or weaker etc. 
                playerMessage("You drank the potion!  " + key + " +" + item.playerEffect[key]);
            
            });
        }
        var cloneItems = [...store.playerItems];
        cloneItems.splice(slot-1, 1);
        var persist = persistGameData({player:clonePlayer, playerItems:cloneItems});
        $.when(persist).done(()=> {
            render();
        });
    }

 
}

function addPlayerItem(item, playerID) {
    var cloneItems = [ ...store.playerItems ];
    var existing = cloneItems.find((cloneItem) => cloneItem.itemID == item.id);
    if (existing) {
        existing.qty ++;
    } else {
        var playerItemObject = {playerID: playerID, itemID: item.id, qty: 1};
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

        return $.post("api/playerItemPOST.php", { playerItems, playerID }, function (result) {
            var dbPlayerItems = JSON.parse(result);
            store.playerItems = dbPlayerItems;
           
        });
    }