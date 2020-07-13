<html>
<body>

Number of widgets ordered <?php echo $_POST["order"]; ?><br>

<?php  
$order = $_POST["order"];
//$widgetPacks = array(250, 500, 1000, 2000, 5000);
// More complicated pack sizes. Some orders to test these with: 2751, 2861, 2880, 310(this was a tricky one), 1310)
$widgetPacks = array(160, 300, 413, 715, 1000, 4500);

// Some preprocessing
array_unique($widgetPacks);
rsort($widgetPacks);
define("ARRAYLENGTH", count($widgetPacks));
define("SMALLESTPACK", $widgetPacks[ARRAYLENGTH - 1]);
define("BIGGESTPACK", $widgetPacks[0]);

echo "<br>Packs to be ordered:<br>";
if($order > 0) {
	// If the order is smaller than the smallest pack, order the smallest pack
	if($order < SMALLESTPACK) {
		echo SMALLESTPACK . " X 1";
	} else {
		// If the order is bigger than the biggest pack, order the big ones until others should be ordered
		$bigPacks = floor($order / BIGGESTPACK);
		$order -= $bigPacks * BIGGESTPACK;

		if ($order < SMALLESTPACK) {
			$smallOrder = array(SMALLESTPACK);
		// If the number of widget packs is 2, only a part of the algorithm has to be run
		} elseif (ARRAYLENGTH == 2) {
			if ($bigPacks > 0) {
				$bigPacks--;
				$order += BIGGESTPACK;
			}
			$smallPacks = array(SMALLESTPACK, SMALLESTPACK);
			$mixedPacks = array(SMALLESTPACK, BIGGESTPACK);
			
			$smallOrder = betterPack($smallPacks, $mixedPacks, $order);
		} else {
			// This is where the array that keeps track of all the boxes is made
			$smallOrder = packsToSend($order, 0, $widgetPacks);
			rsort($smallOrder);
		}
		// Output algorithm. 
		// If the first element of smallOrder is the biggest box, that means only one box was ordered, thus the number of boxes is bigPacks + 1
		if($smallOrder[0] == BIGGESTPACK) {
			echo BIGGESTPACK . "X" . ($bigPacks + 1) . "<br>";
		// Otherwise the number of big boxes is bigPacks.
		} else {
			if($bigPacks != 0) {
				echo BIGGESTPACK . "X" . $bigPacks . "<br>";
			}
			$counter = 1;
			$i = 0;
			while ($i < count($smallOrder) - 1) {
				if ($smallOrder[$i] == $smallOrder[$i + 1]) {
					$counter++;
				} else {
					echo $smallOrder[$i] . " X " . $counter . "<br>";
					$counter = 1;
				}
				$i++;
			}
			echo $smallOrder[$i] . " X " . $counter . "<br>";
		}
	}
} else {
	echo "Incorrect input";
}

// The recursive function which calculates the best possible way to order boxes by the rules of least extra widgets, and smallest number of boxes.
// This algorithm recursively assumes that the box that is smaller than the order will be bought, until the end, where it chooses to either buy 2 
// of the smallest boxes, or one bigger box. After the recursion calls, while going back down the stack, the algorithm checks if it was worth buying
// more small boxes, than one of bigger size.
function packsToSend ($order, $index, $widgetPacks) {
	// The index represents a position which shows the box that is bigger than the order, and the one that is smaller than the order
	while (($widgetPacks[$index] > $order)  && ($index < ARRAYLENGTH - 1)) {
		$index++;
	}
	// If the order is the same as one of the boxes, order the box
	if ($widgetPacks[$index] == $order) {
		return array($order);
	// The stop condition of the recursive algorithm. When it reaches the end of the recursive calls, it finds the most efficient way to buy boxes. 
	// Either 2 small ones, or one big one.
	} elseif ($order - $widgetPacks[$index] < SMALLESTPACK) {
		$smallPacks = array(SMALLESTPACK, SMALLESTPACK);
		$mixedPacks = array(SMALLESTPACK, $widgetPacks[$index]);
		
		// The function which checks which option orders less extra widgets.
		$bestPack = betterPack($smallPacks, $mixedPacks, $order);
		if (($widgetPacks[$index - 1] - $order) <= (array_sum($bestPack) - $order)) {
			return array($widgetPacks[$index - 1]);
		} else {
			return $bestPack;
		}
	}
	
	// The recursive call of the function
	$returnedArray = packsToSend($order - $widgetPacks[$index], $index, $widgetPacks);
	
	// This checks whether buying more small boxes has had less extra widgets, or whether buying a big box will.
	if (($widgetPacks[$index - 1] - $order) <= (array_sum($returnedArray) - $order + $widgetPacks[$index])) {
		return array ($widgetPacks[$index - 1]);
	} else {
		array_push ($returnedArray, $widgetPacks[$index]);
		return $returnedArray;
	}
}

// The function which checks which option orders less extra widgets.
function betterPack($smallPacks, $mixedPacks, $order){
	if($order - array_sum($smallPacks) < $order - array_sum($mixedPacks)) {
		return $smallPacks;
	} else {
		return $mixedPacks;
	}
}

?>
</body>
</html>