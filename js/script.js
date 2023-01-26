var winOrLose = false;

$(".letter").click(function(){
$.ajax({
	data: {letter: $(this).text(), action: 2},
	type: "POST",
	dataType: "json",
	url: "controller.php",
	context: this,
	success: function(data){
		if(!winOrLose)
		{
			if(data.win == null)
			{
				console.log(data.word);
				console.log(data.fLetters);
				$("#hanged").attr("src",data.image); //Se reemplaza la imagen actual con la siguiente
				$("#lives-left").text(data.lives); //Muestra las vidas que van quedando
				$("#guessed-word-div").html(data.guessedWord); //Se revela(n) la(s) letra(s) acertada(s)
				$(this).addClass("display-none"); //Oculta la letra clickeada
			}
			else
			{	
				//Significa que se quedó sin vidas
				if(data.win == false)
				{
					console.log(data.record);
					winOrLose = true;
					$("#lives-left").text(data.lives);
					$("#hanged").attr("src",data.image);
					$("#the-word-was-div").html(data.word);
					$("#the-word-was-div").removeClass("display-none");
					$("#play-again-div").removeClass("display-none");
				}
				else
				{
					console.log(data.win);
					$("#actual-points").text(data.actualPoints);
					$("#record-points").text(data.record);
					$("#guessed-word-div").html(data.guessedWord);
					$("#next-word-div").removeClass('display-none');
				}
			}
		}
	},
	error: function (jqXHR, textStatus, errorThrown)
	{
	alert(textStatus);
	}
}); 
}); 
