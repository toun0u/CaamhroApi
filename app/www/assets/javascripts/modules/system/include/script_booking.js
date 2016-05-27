// Implantation du calendrier
var calend = new CalendarPopup("calendardiv");
calend.setCssPrefix("CAL_STYLE"); calend.setYearSelectStartOffset(0); calend.setWeekStartDay(1); calend.setDayHeaders("D","L","M","M","J","V","S"); calend.setTodayText("Aujourd'hui"); calend.setMonthNames("Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");

/*
today = new Date;
mo = today.getMonth()+1;
day = today.getDate() - 1;
year = today.getFullYear();

if (day == 0){
 mo--;
 if (mo == 0){ mo = 12; year = year-1; }
 if (mo == 2){ if ((year%4) == 0) day = 29; else day = 28; } else { if ((mo == 1) || (mo == 3) || (mo == 5) || (mo == 7) || (mo == 8) || (mo == 10) || (mo == 12)) day = 31; else day = 30; }
}
date = year+"-"+mo+"-"+day;
calendExp.addDisabledDates(null, date);
calend.addDisabledDates(null, date);
yearend = year + 1; dayend = day + 1; datefinexp = yearend + "-" + mo + "-28";
calendExp.addDisabledDates(datefinexp, null);
mo = mo + 6; if (mo > 12){ year++; mo -= 12; } datefintrn = year + "-" + mo + "-28";
calend.addDisabledDates(datefintrn, null);

//Gestion des calendriers
function calendar(champ,id,type){ if (document.saisie.btheadt[0].checked) calend.select(champ,id,type); else calendExp.select(champ,id,type); }
function calendmaj(idin,idout){ idin.value = idout.value; }
*/


