const daySel = document.getElementById("day");
const docSearch = document.getElementById("doctorSearch");
const degreeInp = document.getElementById("degree");
const docNameInp = document.getElementById("doctorName");
const langSel = document.getElementById("language");

const pvDay = document.getElementById("pvDay");
const pvName = document.getElementById("pvName");
const pvDegree = document.getElementById("pvDegree");
const pvCaption = document.getElementById("pvCaption");

function updatePreview() {
  const d = daySel.value || "Day 1";
  const name = (docNameInp.value || "").trim() || "Doctor Name";
  const deg = (degreeInp.value || "MD").trim();
  const lang = langSel.value;

  pvDay.textContent = d;
  pvName.innerHTML = `${name}, <span id="pvDegree">${deg}</span>`;
  pvCaption.textContent = `Medical Content for ${d} ${
    lang ? "(" + lang + ")" : ""
  }`;
}

docSearch.addEventListener("input", () => {
  const pick = docSearch.value.trim();
  if (pick) {
    docNameInp.value = pick;
    updatePreview();
  }
});

daySel.addEventListener("change", updatePreview);
docNameInp.addEventListener("input", updatePreview);
degreeInp.addEventListener("input", updatePreview);
langSel.addEventListener("change", updatePreview);

updatePreview();
