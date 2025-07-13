document.addEventListener("DOMContentLoaded", () => {
  const iconMap = ["banana", "seven", "cherry", "plum", "orange", "bell", "bar", "lemon", "melon"];
  const iconHeight = 79;
  const numIcons = iconMap.length;
  const timePerIcon = 100;
  const indexes = [0, 0, 0];

  const reels = document.querySelectorAll(".reel");
  const spinBtn = document.getElementById("spinButton");
  const autoSpinCheckbox = document.getElementById("autoSpin");
  const winText = document.getElementById("winText");
  const slotsEl = document.querySelector(".slots");
  const chipsDisplay = document.getElementById("chip-display");

  let autoSpinInterval = null;
  const spinCost = 100;

  function getChips() {
    const match = chipsDisplay.textContent.match(/Chips:\s*(\d+)/);
    return match ? parseInt(match[1], 10) : 0;
  }

  function updateChipsDisplay(value) {
    chipsDisplay.textContent = `💰 Chips: ${value}`;
  }

  function roll(reel, offset = 0) {
    const delta = (offset + 2) * numIcons + Math.floor(Math.random() * numIcons);
    const currentY = parseFloat(getComputedStyle(reel).backgroundPositionY || "0") || 0;
    const targetY = currentY + delta * iconHeight;
    const duration = 800 + delta * timePerIcon;
    const normY = targetY % (numIcons * iconHeight);

    return new Promise((resolve) => {
      setTimeout(() => {
        reel.style.transition = `background-position-y ${duration}ms cubic-bezier(.41,-0.01,.63,1.09)`;
        reel.style.backgroundPositionY = `${targetY}px`;
      }, offset * 150);

      setTimeout(() => {
        reel.style.transition = "none";
        reel.style.backgroundPositionY = `${normY}px`;
        resolve(delta % numIcons);
      }, duration + offset * 150);
    });
  }

  function checkWin() {
    const a = indexes[0];
    const b = indexes[1];
    const c = indexes[2];

    if (a === b && b === c) {
      return { multiplier: 100, message: `3 gleiche Symbole (${iconMap[a]})! x100 Einsatz` };
    } else if (a === b || a === c || b === c) {
      const match = a === b ? iconMap[a] : (a === c ? iconMap[a] : iconMap[b]);
      return { multiplier: 5, message: `2 gleiche Symbole (${match})! x5 Einsatz` };
    } else {
      return { multiplier: 0, message: "Kein Gewinn." };
    }
  }

  async function spinGame() {
    let chips = getChips();
    if (chips < spinCost) {
      winText.textContent = "Nicht genug Chips!";
      return;
    }

    spinBtn.disabled = true;
    winText.textContent = "Drehe...";
    slotsEl.classList.remove("win1", "win2");

    chips -= spinCost; // ✅ Direkt Abziehen
    updateChipsDisplay(chips);

    const results = await Promise.all([...reels].map((reel, i) => roll(reel, i)));
    results.forEach((val, i) => indexes[i] = (indexes[i] + val) % numIcons);

    const winResult = checkWin();
    winText.textContent = winResult.message;

    // Gewinnauszahlung
    if (winResult.multiplier === 5) {
      chips += spinCost * 5;
      slotsEl.classList.add("win1");
    } else if (winResult.multiplier === 100) {
      chips += spinCost * 100;
      slotsEl.classList.add("win2");
    }

    updateChipsDisplay(chips);

    await fetch("slot.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `spin=true&newchips=${chips}`
    });

    spinBtn.disabled = false;
  }

  spinBtn.addEventListener("click", spinGame);

  autoSpinCheckbox.addEventListener("change", () => {
    if (autoSpinCheckbox.checked) {
      if (!autoSpinInterval) {
        autoSpinInterval = setInterval(() => {
          if (!spinBtn.disabled) spinGame();
        }, 3500);
      }
    } else {
      clearInterval(autoSpinInterval);
      autoSpinInterval = null;
    }
  });
});