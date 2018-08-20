function startGenerate() {

  var type = $('#signal_type').val();
  var generated = generate(type);
  console.log(generated.regular);
  console.log(generated.long);

  var html = '<tr scope="row"><td>' + type + '</td><td>' + generated.regular + '</td><td>' + generated.long + '</td></tr>';

  $('#restable').append(html);
}

function calcRepeats() {
  var code = $("#usercode").val();
  var repeats = getRepeats(code);
  $("#repeats").html(repeats);
}

function generateNewRepeat() {
  var code = $("#usercode").val();
  var repeats = $("#newrepeat").val();
  var newCode = getNewCode(code, repeats);
  $("#newcode").val(newCode);
}


function startGenerateLivolo(){
    var remote = $('#remoteId').val();
    if(remote == ""){
        alert("please select remote id");
        return;
    }
    var btn =  $('#liv_btn').val();
    var code = generateLivolo(remote, btn);
    var html = '<tr scope="row">'+ '<td>' + 'Livolo' + '</td>' +'<td>' + code + '</td>';

    $('#livtable').append(html);
}
function start_bin2gen() {
	var bin_str=$('#bin_str').val();
	var min_l=$('#min_l').val();
	var prot=$('#prot').val();
	var repeats=$('#repeats').val();
	var pause=$('#pause').val();
	var code=generate_bin2rm(bin_str, min_l, prot, repeats, pause);
	var code64=hexToBase64(code);
	var html = '<tr scope="row">'+ '<td>' + 'Code string' + '</td>' +'<td>' + code + '</td>'+'<td>' + code64 + '</td>';
	$('#livtable').append(html);
}