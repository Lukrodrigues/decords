<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['AlunoEmail']) and !isset($_SESSION['AlunoSenha'])) {
	echo "é necessario login";
	header("Location: index.php");
	exit;
}
?>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Decords</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Bootstrap -->
	<link rel="icon" href="img/favicon-96x96.png">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
</head>

<body>
	<nav class="navbar navbar-inverse navbar" role="navigation">
		<div class="container">
			<div class="row">
				<div class="navbar-header">
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
					</button>

					<a class="navbar-brand" href="index.php"><img id="logo" src="img/foto22.jpg" width="100" height="30"></a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Tutorial <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="tutorial-01.php">Tutorial-01</a></li>
								<li class="divider"></li>
								<li><a href="tutorial_02.php">Tutorial-02</a></li>
								<li class="divider"></li>
							</ul>
						<li class="dropdown"><a class="dropdown-toggle" href="#" data-toggle="dropdown">Exercicios <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="iniciantes.php">Iniciantes</a></li>
								<li class="divider"></li>
								<li><a href="intermediarios.php">Intermediarios</a></li>
								<li class="divider"></li>
								<li><a href="avancados.php">Avancados</a></li>
								<li class="divider"></li>
							</ul>
						<li class="active"><a href="login.php">Sair</a></li>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>

	<body>
		<script>
			function bigImg(x) {
				x.style.height = "400px";
				x.style.width = "600px";
			}

			function normalImg(x) {
				x.style.height = "140px";
				x.style.width = "440px";
			}
		</script>

		<body>

			<h1 style="text-align:center">Introdução Violão:</h1>
			<div class="container inicial">
				<div class="row-fluid">
					<div class="col-lg-6 text-left">
						<h2>Corpo do violão</h2>
						<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://canone.com.br/images/stories/users/62/violao/partes_violao.png" alt="" width="540" height="140" </br>
						<h4>Para ver mais sobre cada uma das partes, acompanhe as explicações logo abaixo:</h4>
						<li><b>Corpo:</b> É o local onde está a boca, o rastilho e o cavalete. Em violões elétricos, também
							são encontradas outras partes neste local, como a saída, os botões de ajuste,etc.</li>
						<li><b>Boca:</b> É o "buraco" que está no meio do corpo do violão. É por este local que o
							som se propaga.</li>
						<li><b>Rastilho:</b> É a parte do violão que prende uma da extremidades das cordas. É importante
							que esteja na altura certa.</li>
						<li><b>Cavalete:</b> O cavalete mantém o rastilho na altura correta.</li>
						<li><b>Braço:</b> É composto basicamente pela mão, tarraxas, trastes, casas e pestana.</li>
						<li><b>Mão:</b> É a extremidade do braço. Neste local estão as tarraxas e uma das extremidades das cordas.</li>
						<li><b>Tarraxas:</b> São as peças localizadas na mão que servem para afinar as cordas. Elas são seis, sendo uma para cada corda. Conforme você girá-las, a corda ficará
							mais apertada, o que mudará o seu som. São indispensáveis para que seu instrumento fique bem afinado antes de tocar.</li>
						<li><b>Trastes:</b> São as barrinhas de metal que se localizam em toda a escala. Elas separam as casas e é muito importante que elas estejam bem colocadas para uma
							boa afinação da guitarra. Com o tempo você pode trocá-las, caso fiquem desgastadas, fora do local certo, etc.</li>
						<li><b>Casas:</b> As casas são os espaços localizados entre os trastes, que são pressionadas durante toda a música. A variação do local que for pressionado, fará mudar
							o som, variando os acordes. São nelas que estão localizadas as notas musicais.</li>
						<li><b>Pestana:</b> Esta peça não está exatamente na escala, mas sim no local de separação entre a mão e a contiuação do braço. Nela ficam apoiadas as cordas, e ela pode
							ser "substituída" com o uso dos dedos ou de instrumentos apropriados para tal.</li> </br>
						<li><b>A: Lá</b>
							<h4>Acorde:</h4>
							<img class="img-responsive" src="http://www.michael.com.br/site/blog/wp-content/uploads/2015/05/la3.png" alt="" width="400" height="140"></br>
						<li><b>B: Sí</b>
							<h4>Acorde:</h4>
							<img class="img-responsive" src="http://3.bp.blogspot.com/-Mh4KuOTlKpA/TVvT-2xo6kI/AAAAAAAAAIM/uRZKtbQ4mm0/s1600/Si.png" alt="" width="400" height="140"></br>
							<p>A nota Si(B) também possui a pestana, que é o dedo 1 prendendo várias cordas de uma vez,somente serão presas da primeira até a quinta corda.</p>
							<h4>Técnica de Afinação:</h4>
							<p><b>A afinação de cada corda é determinada pelo comprimento, espessura e grau de tensão que variam de corda para corda. Convém afinar o instrumento sempre antes de
									tocar usando um afinador eletrônico ou por algum referencial, como diapasão ou teclado.</b>
								<p />
							<p><b>Ele lhe dá uma nota referencial. Ex: Lá (A) que é a 5ª corda solta. Tendo-a como base, você reproduz o som da corda abaixo solta e, assim, vá ajustando-as pela tarraxa, para
									cima ou para baixo, até o som se assemelhar sucessivamente. Com as exceções da 3ª corda, que toca-se na 4ª casa para afinar a 2ª, e a 1ª corda, que será o referencial para a 6ª corda.</b></p>
							<img class="img-responsive" src="http://www.portalmusica.com.br/wp-content/uploads/2013/03/afinar-de-ouvido.jpg" alt="" width="400" height="140"></br>
							<h4>Exercício Básico Paleta:</h4>
						<li>Ataca a corda somente para Baixo</li>
						<li>Ataca a corda somente para Cima</li>
						<li>Ataca a corda alternando para Baixo e Cima</li>
						<img class="img-responsive" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRby5qGoCypbcVmtCqXsgvxgrutw8f_9r0pb-ydBRRWi4iT-9UX" alt="" width="150" height="240"></br>
						<h4>Tecnica da mão direita</h4>
						<img class="img-responsive" src="http://2.bp.blogspot.com/-VGaIBAi5OLU/T1VCXHjSXCI/AAAAAAAAAPI/1htqPhY9U1M/s1600/posicao-mao-direirta.jpg" alt="" width="200" height="500"></br>
						<p class="pull-right"><a href="#">Back to top</a></p></br>
						<h4>Como Ler Tablatura:</h4>
						<p><b>As tablaturas são a maneira mais simples de representar solos de violão e guitarra.</b></p>
						<p><b> No primeiro contato com as tablaturas é necessário saber que existem 6 linhas paralelas
								onde cada uma representa uma corda do violão. De cima para baixo, a primeira linha representa a primeira corda (mizinho),
								enquanto a sexta linha representa a sexta corda (mizão). Veja abaixo uma representação de tablatura sem nenhuma nota:</b></p>
						<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://oguitarrista.com/img_public/tab03.jpg?980d22" alt="" width="440" height="140"></br>
						<h4>Técnica para Ler Partituras:</h4>
						<p><b>A leitura de partitura não é essencial para o músico, mas o seu domínio sem dúvida enriquece o vocabulário musical.
								Grande parte do repertório composto nunca foi registrado em áudio e continua disponível apenas no formato escrito.
								Conseguir ler partitura permite o contato com milhares de músicas, dos mais variados estilos e épocas, muitas vezes esquecidas
								ou que nunca caíram na graça do popular.</b></p>
						<p><b>O registro em partitura é uma imagem das vontades do compositor, um lembrete de como a música deverá ser interpretada, como o seu corpo está estruturado.
								É um desmembramento dos pequenos fragmentos que formam uma composição expostos de maneira clara, direta e universal.</b></p>
						<p><b>As notas são colocadas de forma alternada, uma em cima de uma linha, a próxima num espaço, numa linha, num espaço,
								e assim por diante. As notas escritas em baixo serão as mais graves e as notas escritas em cima as mais agudas.</b></p>
						<p><b>Se necessário, pode-se desenhar linhas complementares, tanto acima quanto abaixo do pentagrama.
								Não existe um número máximo de linhas complementares, isso vai depender do alcance do instrumento.</b></p>
						<h4>Entendendo alteração de notas no Braço do Violão:</h4>
						<p><b>Usamos um termo musical chamado tom para medir quantas casas percorremos no braço, ou seja, a cada casa somamos ½ tom, ou seja.</b></p>
						<li><b>Ao se deslocar da casa 1 para a casa 2 percorremos ½ tom.<b>
						<li><b>Ao se deslocar da casa 8 para a casa 9 percorremos ½ tom.<b>
						<li><b>Ao se deslocar da casa 6 para a casa 5 percorremos ½ tom.<b>
						<li><b>Ao se deslocar da corda solta para a casa 1 percorremos ½ tom<b>
						<li><b>Ao se deslocar da casa 1 para a casa 3 percorremos ½ tom + ½ tom, ou seja, 1 tom.</b>
							<h4>Exemplo:</h4>
							<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://www.deniswarren.com/wp-content/uploads/Notas-no-braco-do-instrumento-TONS-1024x296.jpg" alt="" width="440" height="140"></br></br>
							<h4>Alteração de notas:(#) SUSTENIDO</h4>
							<p>O simbolo (#)chama-se sustenido e serve para aumentar um semitom (1 casa) a nota natural</p>
							<p>Podemos comparar a partitura em cima com a tablatura a baixo para ficar mais facil assimilação para alteração em Sustenidos(#):</p>
							<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://www.deniswarren.com/wp-content/uploads/Partitura-com-Tablatura-6-cordas-Sustenido.jpg" alt="" width="440" height="140"></br></br>
							<p class="pull-right"><a href="#">Back to top</a></p></br>
							<h4>Compasso Musical:</h4>
							<p>O compasso musical é definido como um elemento que divide a música em intervalos de tempo iguais ou variáveis, com o objetivo de organizar a estrutura e facilitar a orientação,
								permitindo ao intérprete ou ouvinte organizar o ritmo de uma música.</p>
							<p>Outra coisa essencial que devemos entender e lembrar é que o número de tempos de um compasso não precisa necessariamente ser o mesmo número de notas, ou seja,
								dizer que se têm três batidas em um compasso não quer dizer que se têm três notas.</p>
							<h4>Compasso Simples</h4>
							<p>Compasso cujos tempos podem ser divididos por dois, ou também podemos dizer que a unidade de tempo do compasso pode ser naturalmente repartida em duas partes iguais.</p>
							<h4>Exemplo Compasso Simples:</h4>
							<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://upload.wikimedia.org/wikipedia/commons/thumb/9/9b/CompassoBinario-TernarioSimples.png/800px-CompassoBinario-TernarioSimples.png" alt="" width="440" height="140"></br></br>
							<h4>Compasso Composto:</h4>
							<p>Compasso composto é aquele em que cada unidade de tempo é subdividida em três notas, cuja duração é definida pelo denominador da fórmula de compasso.</p></br>
							<h4>Exemplo Compasso Composto:</h4>
							<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://images.slideplayer.com.br/2/5636031/slides/slide_33.jpg" alt="" width="440" height="140"></br></br>
							<h4>Pausas Musicais ou Figura do Silêncio:</h4>
							<p>Pausas musicais são intervalos de tempo em que deve haver silêncio, ou seja, nenhuma nota deve ser tocada nesse instante.</p>
							<p>Cada uma das pausas musicais ou figuras de silêncio terá uma duração proporcional, assim como acontece com as figuras rítmicas. Terão também o mesmo nome,
								apenas precedido pela designação de pausa, por exemplo: uma pausa de semibreve, pausa de mínima, pausa de semínima e assim por diante.</p>
							<P>Conforme figura a baixo:</P>
							<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://3.bp.blogspot.com/-q0SC_zod_ho/UbdsWq8_ztI/AAAAAAAAEKo/V7juhkH9b_8/s1600/figmus.jpg" alt="" width="440" height="140"></br></br>
							<p class="pull-right"><a href="#">Back to top</a></p></br>
					</div>
				</div>



				<div class="col-lg-6 text-left">
					<div class="row-fluid">
						<h2>Braço do violão e Notas</h2>
						<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://3.bp.blogspot.com/-HmZJt5i3sUo/UxiSYjlwWdI/AAAAAAAAFRw/IP-qOWGL-tI/s1600/violao_tablatura.jpg" alt="" width="400" height="200"></br>
						<h4>As notas musicais do violão são 7 e representadas:</h4>
						<li><b>C: Dó</b>
							<h4>Acorde:</h4>
							<img class="img-responsive" src="http://3.bp.blogspot.com/-gbkd3jr2hc0/TVvJQQFssxI/AAAAAAAAAHo/Z1Da2PHEYiw/s1600/D%25C3%25B3.png" alt="" width="400" height="140"></br>
						<li><b>D: Re</b>
							<h4>Acorde:</h4>
							<img class="img-responsive" src="http://1.bp.blogspot.com/-X3Lvs_EvLBk/TVvMP_rMn7I/AAAAAAAAAHs/Gfyk_nJC2s4/s1600/R%25C3%25A9.png" alt="" width="400" height="140"></br>
						<li><b>E: Mi</b>
							<h4>Acorde:</h4>
							<img class="img-responsive" src="http://1.bp.blogspot.com/-jRNURwtoN0U/TVvNjiU86hI/AAAAAAAAAH4/pFF2GRI1Hj8/s1600/Mi.png" alt="" width="400" height="140"></br>
						<li><b>F: Fá</b>
							<h4>Acorde:</h4>
							<h4>F (Fá)</h4>Para colocar a nota FÁ é preciso prender as seis cordas na casa 1 com o dedo 1</br> como mostra a seta:
							<img class="img-responsive" src="http://2.bp.blogspot.com/-wRhA4ZMA41M/TVvNXMqNs4I/AAAAAAAAAH0/5jZTtfvDqQM/s1600/F%25C3%25A1.png" alt="" width="400" height="140"></br>
						<li><b>G: Sol</b>
							<h4>Acorde:</h4>
							<img class="img-responsive" src="http://3.bp.blogspot.com/-s7m0N15zXhM/TVvTjwZ_cwI/AAAAAAAAAIE/qmVK7VpxrNk/s1600/Sol.png" alt="" width="400" height="140"></br>
							<h4>Tecnica de Palheta:</h4>
							<p>Segure a palheta entre a polpa do polegar e o lado da junta da primeira falange do dedo indicador. A ponta da palheta deve ficar a um ângulo de mais ou menos 90º em
								relação às cordas. Os dedos devem "agarrar" a palheta de modo firme, mas relaxado.Se os dedos ficarem muito rígidos será difícil movê-los rapidamente, mas, se não agarrá-la
								com suficiente firmeza, você poderá deixar cair a palheta ou fazer com que ela se mexa enquanto toca. </p>
							<img class="img-responsive" src="http://files.teachguitar.webnode.com.br/200000032-3971a3a680/palheta.png" alt="" width="440" height="140"></br>
							<h4>Tecnica da mão direita/esquerda:</h4>
							<img class="img-responsive" src="http://2.bp.blogspot.com/-5vmNsDoT8_o/TiH1qIx-tWI/AAAAAAAAAJM/HVNFzBIJSr0/s320/Sem%2Bt%25C3%25ADtulo.png" alt="" width="300" height="140"></br>
							<h4>Tecnica da mão esquerda</h4>
							<img class="img-responsive" src="http://primeirosacordes.com.br/images/stories/imagembaixo/posio%20mo%20esquerda.png" alt="" width="300" height="140"></br></br>
							<h4>Imagem do Violão Tablatura:</h4>
							<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://oguitarrista.com/img_public/tab04.jpg" alt="" width="440" height="140"></br>
							<h4>Exemplo da Tablatura</h4>
							<p>A partir dessa representação, cada número inserido nas linhas representa a casa a ser tocada em sua respectiva casa,
								sendo o número 0 (zero) uma indicação de que a corda deve ser tocada solta.
								Os números representados na mesma coluna, ou seja, na mesma direção vertical, devem ser tocados no mesmo instante.</p>
							<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://www.aprendaguitarra.mus.br/wp-content/uploads/2015/01/tablatura.jpg" alt="" width="440" height="140"></br></br>
							<h4>Pentagrama Musical de Partituras:</h4>
							<p>E´ formado por 5 linhas horizontais e 4 espaços onde as notas de uma música são escritas.</p>
							<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://images.comunidades.net/mus/musicar/pentagrama.png" alt="" width="440" height="140"></br></br>
							<p>Comece com a clave de sol. Uma das primeiras coisas que você encontrará ao ler partituras para tocar violão será a clave. </p>
						<li>As cinco linhas, de baixo para cima, representam as notas: E (mi), G (sol), B (si), D (ré), F (fá).</li>
						<li>Os quatro espaços, de baixo para cima, representam as notas: F (fá), A (lá), C (dó), E (mi).</li></br>
						<li>Um macete para identificar rapidamente as notas no pentagrama é memorizar as notas que ficam nos espaços: Fá, Lá, Dó, Mi. Basta lembrar da frase – “Fala do Mi”.</li></br>
						<img class="img-responsive" src="http://www.deniswarren.com/wp-content/uploads/Leitura-de-Partitura-Imagem-6-300x171.jpg" alt="" width="300" height="140"></br></br>
						<li>Depois do Fá temos a nota sol, antes do Fá a nota Mi.</li>
						<li>Depois do Lá temos a nota si, antes do Lá a nota Sol.</li>
						<li>Depois do Dó temos a nota Ré, antes do Dó a nota Si.</li>
						<li>Depois do Mi temos a nota Fá, antes do Mi a nota Ré.</li></br>
						<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://www.deniswarren.com/wp-content/uploads/Leitura-de-Partitura-Imagem-7.jpg" alt="" width="440" height="140"></br></br>
						<h4>Alteração de notas:(b) BEMOL</h4>
						<p>O simbolo (b)chama-se bemol e serve para diminuir um semitom (1 casa) a nota natural</p>
						<p>Podemos comparar a partitura em cima com a tablatura a baixo para ficar mais facil assimilação para alteração em Bemois(b):</p>
						<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://www.deniswarren.com/wp-content/uploads/Partitura-com-Tablatura-6-cordas-Bemol-1024x700.jpg" alt="" width="440" height="140"></br>
						<h4>Compasso Binário:</h4>
						<p>Formados por dois tempos, esse compasso tem duas batidas, sendo a primeira forte e a segunda fraca.</p>
						<img class="img-responsive" src="http://www.portalmusica.com.br/wp-content/uploads/2011/08/compasso_binario.jpg" alt="" width="340" height="40"></br>
						<h4>Compasso Ternário:</h4>
						<p>Formados por três tempos, esse compasso tem três batidas, sendo a primeira forte, a segunda e terceira fracas.</p>
						<img class="img-responsive" src="http://www.portalmusica.com.br/wp-content/uploads/2011/08/compasso_ternario-e1312661906458.jpg" alt="" width="340" height="40"></br>
						<h4>Compasso Quaternário:</h4>
						<p>Formados por quatro tempos, esse compasso tem quatro batidas, sendo a primeira forte, a segunda fraca, a terceira de intensidade média e a quarta fraca.</p>
						<img class="img-responsive" src="http://www.portalmusica.com.br/wp-content/uploads/2011/08/compasso_quaternario.jpg" alt="" width="340" height="40"></br>
						<h4>Duração de Notas:</h4>
						<img class="img-responsive" onmouseover="bigImg(this)" onmouseout="normalImg(this)" border="0" src="http://violaoparainiciantes.com/wp-content/uploads/2015/09/representacao_do_valor_das_notas_musicais.jpg" alt="" width="440" height="140"></br>
						<h4>Exemplo de diferentes compassos e duração de notas:</h4>
						<img class="img-responsive" src="http://violaoparainiciantes.com/wp-content/uploads/2015/09/representacoes_compasso_quatro_por_quatro.jpg" alt="" width="540" height="140"></br>


					</div>
				</div>

		</body>

	</body>

</html>