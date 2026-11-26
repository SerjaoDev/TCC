const ctx = document.getElementById('grafico');
new Chart(ctx,{
type:'line',
data:{
labels:['Seg','Ter','Qua','Qui','Sex'],
datasets:[{
labels: nomesTurmas,
data: mediasTurmas,
borderWidth:4,
tension:.4,
fill:false
}]
},
options:{
responsive:true
}
});