<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Perfil e biografia do Professor José A. Homrich - Projeto Decords Música e Teoria.">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<title>Decords - Professor</title>

	<!-- Tailwind CSS -->
	<script src="https://cdn.tailwindcss.com"></script>

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Inter', sans-serif;
		}

		/* Hover Dropdown Desktop */
		@media (min-width: 768px) {
			.group:hover .group-hover\:block {
				display: block;
			}
		}

		/* Estilo da biografia */
		#professor-bio p {
			text-indent: 50px;
			text-align: justify;
			margin-bottom: 1.5rem;
			line-height: 1.75;
		}
	</style>
</head>
<!-- ADJUSTE IMPORTANTE -->
<!-- pt-24 = compensação EXATA da navbar fixa (altura real ~ 96px) -->

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen pt-24">

	<!-- NAVBAR FIXA E SEM ESPAÇOS -->
	<nav class="bg-blue-900 text-white shadow-lg fixed top-0 left-0 w-full z-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex justify-between h-20 items-center">

				<!-- LOGO -->
				<div class="flex-shrink-0 flex items-center gap-3">
					<a href="index.php" class="flex items-center gap-3 group">
						<img src="img/foto22.jpg"
							class="h-12 w-auto rounded-lg border-2 border-blue-400 group-hover:border-white transition object-cover">
						<span class="text-xl font-bold tracking-wide">DECORDS</span>
					</a>
				</div>

				<!-- MENU DESKTOP -->
				<div class="hidden md:flex items-center space-x-6">
					<!-- Dropdown Categorias -->
					<div class="relative group h-full py-6 cursor-pointer">
						<span class="hover:text-blue-300 font-medium flex items-center transition">
							Categorias <i class="fas fa-chevron-down ml-1 text-xs"></i>
						</span>
						<!-- Submenu -->
						<div class="absolute top-16 left-0 w-48 bg-white text-gray-800 shadow-xl rounded-b-md hidden group-hover:block border-t-4 border-blue-500 animate-fade-in">
							<a href="historico.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition"><i class="fas fa-history mr-2 text-blue-500"></i>Histórico</a>
							<a href="professor.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition"><i class="fas fa-chalkboard-teacher mr-2 text-blue-500"></i>Professor</a>
							<a href="fotos.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition"><i class="fas fa-images mr-2 text-blue-500"></i>Fotos</a>
							<a href="videos.php" class="block px-4 py-3 hover:bg-gray-100 transition"><i class="fas fa-video mr-2 text-blue-500"></i>Vídeos</a>
						</div>
					</div>

					<a href="contato.php" class="hover:text-blue-300 font-medium transition">Contato/Agenda</a>

					<!-- Dropdown Exercícios -->
					<div class="relative group h-full py-6 cursor-pointer">
						<span class="hover:text-blue-300 font-medium flex items-center transition">
							Exercícios <i class="fas fa-chevron-down ml-1 text-xs"></i>
						</span>

						<div class="absolute top-16 left-0 w-56 bg-white text-gray-800 shadow-xl rounded-b-md hidden group-hover:block border-t-4 border-blue-500">
							<a href="login.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition">
								<i class="fas fa-user-graduate mr-2 text-blue-600"></i>Sou Aluno/Usuário
							</a>
							<a href="login_professor.php" class="block px-4 py-3 hover:bg-gray-100 transition">
								<i class="fas fa-chalkboard-teacher mr-2 text-blue-600"></i>Sou Professor
							</a>
						</div>
					</div>

					<a href="comentarios.php" class="hover:text-blue-300 font-medium transition">Comentários</a>
				</div>

				<!-- ÍCONES SOCIAIS -->
				<div class="hidden md:flex items-center space-x-4 border-l border-blue-700 pl-6">
					<a href="#" class="text-gray-300 hover:text-white transition"><i class="fab fa-facebook text-xl"></i></a>
					<a href="#" class="text-gray-300 hover:text-red-500 transition"><i class="fab fa-youtube text-xl"></i></a>
					<a href="#" class="text-gray-300 hover:text-orange-400 transition"><i class="fab fa-blogger text-xl"></i></a>
				</div>

				<!-- Botão Mobile -->
				<div class="md:hidden">
					<button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
						<i class="fas fa-bars text-2xl"></i>
					</button>
				</div>

			</div>
		</div>

		<!-- MENU MOBILE -->
		<div id="mobile-menu" class="hidden md:hidden bg-blue-800 border-t border-blue-700">
			<div class="px-4 py-4 space-y-2">

				<p class="text-gray-300 uppercase text-xs">Navegação</p>
				<a href="historico.php" class="block py-2 px-2 text-white">Histórico</a>
				<a href="professor.php" class="block py-2 px-2 text-white bg-blue-700 font-bold">Professor</a>
				<a href="fotos.php" class="block py-2 px-2 text-white">Fotos</a>
				<a href="videos.php" class="block py-2 px-2 text-white">Vídeos</a>
				<a href="contato.php" class="block py-2 px-2 text-white">Contato/Agenda</a>

				<p class="text-gray-300 uppercase text-xs mt-2">Acesso</p>
				<a href="login.php" class="block py-2 px-2 text-white">Aluno</a>
				<a href="login_professor.php" class="block py-2 px-2 text-white">Professor</a>
			</div>
		</div>

	</nav>

	<!-- Conteúdo Principal - Perfil do Professor -->
	<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

		<header class="text-center mb-12">
			<h1 class="text-4xl font-extrabold text-blue-900">Professor José A. Homrich</h1>
			<h2 class="text-xl text-gray-600">Músico, Educador e Fundador do Projeto Decords</h2>
			<div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
		</header>

		<section class="grid grid-cols-1 md:grid-cols-12 gap-10" id="professor-bio">

			<!-- IMAGEM CORRIGIDA — TAMANHO PADRÃO DAS OUTRAS PÁGINAS -->
			<div class="md:col-span-4">
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto25.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto9.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto10.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto17.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto23.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto24.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto34.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto37.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto39.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto38.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto3.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
			</div>

			<!-- Biografia Detalhada -->
			<div class="md:col-span-8 lg:col-span-8 text-lg text-gray-700">

				<p>
					O Projeto é conduzido pelo Professor José A. Homrich que é Licenciado em Música pela UFRGS e pós graduando em Educação Inclusiva. Músico, professor de música, educador, instrumentista, cantor, compositor, arranjador, poeta e produtor musical. Nascido em Sobradinho, RS teve seus primeiros contatos com música muito cedo, aos 6 anos, em uma igreja católica, na qual cantava e por causa das inúmeras demontrações de “talento musical” (musicalidade) ganhou seu primeiro violão, nesta mesma época.
				</p>
				<p>
					Não foi matriculado em aulas de música ou algo do gênero nesta época, mesmo assim, criança ainda, escutava e copiava músicas em Português, Espanhol e em Inglês para aprender a executá-las. Logo começava a fazer apresentações musicais em escolas, creches, dentre outros, por convite dos próprios professores que o ouviam cantar pelos corredores da escola, vindo a ficar conhecido no bairro em que residia já aos 10 anos, fazendo inúmeras apresentações amadoras em eventos diversos no município de Canoas.
				</p>
				<p>
					A dedicação a um instrumento musical veio mais tarde, com a aprendizagem de forma autodidata do violão. Então, após alguns anos estudando guitarra e esmerando-se na parte técnica sob influência de grandes guitarristas de rock, tocando em bandas Gauchescas, de Rock / Metal e em Bailes, o músico veio a adquirir a Carteira Profissional Prática / Teórica de Músico, a qual indicava como especialidade guitarra e canto e a fazer cursos de música na sede da OMB Porto Alegre.
				</p>
				<p>
					Surgiu o sonho de ser músico profissional e viver dignamente da música e logo percebeu as inúmeras dificuldades que teria, mas nunca desistiu e todas as conquistas foram demoradas e sofridas, ampliadas pela falta de estudo direcionado nos primeiros anos. Nessa mesma época iniciou cursos na OMB (Ordem dos Mùsicos do Brasil), cantou por 3 anos no Grupo Vocal Ulbra, iniciou aula particular de música e curso de extensão na UFRGS (Universidade Federal do Rio Grande do Sul).
				</p>
				<p>
					Deu aulas na Academia de Música Coopen por 11 anos – guitarra e violão, no Espaço de Educação Musical por 4 anos – guitarra, violão, contrabaixo elétrico e técnica vocal, no Colégio Salesiano Dom Bosco por 3 anos – violão, guitarra, teoria musical e música em conjunto, além de aulas particulares de violão, guitarra, contrabaixo elétrico e canto nos últimos 16 anos. Tornou-se Músico, arranjador, produtor musical e compositor. É autor do Hino Municipal da cidade de Nova Santa Rita (escolhido em concurso público em Julho de 2002), de hinos de escola e Formado em Licenciatura Musical / Habilitação Cordas da UFRGS - Universidade Federal do Rio Grande do Sul.
				</p>
				<p>
					Na UFRGS estudou violão erudito, canto coral, regência, história da música, harmonia, forma e análise, educação musical em vários níveis e faixas etárias, dentre tantos outros pormenores da música, além de diferentes gêneros da música universal como Chôro, a própria Música Erudita, ritmos Brasileiros e mundiais como: Rock, Hard Rock, Heavy Metal, MPB, Tango, Ritmos Nacionais diversos, como a Música Regionalista, seus diversos ritmo e a música Brasileira em geral, como MPB, Bossa Nova, Samba, etc.
				</p>
				<p>
					Tocou em diversas bandas de vários estilos como Sombras na Escuridão, Dheva, Luminitos, 7 Vidas, John Rex, Musical Geração e por vários anos na banda de pop melódico HILLEYES, onde cantava, tocava guitarra, compunha as músicas e os arranjos. Cantou também por 3 anos no Grupo Vocal da Ulbra e para uma produtora de eventos em formaturas, shows, casamentos, etc. Atuou também na noite de Porto Alegre e Canoas em shows e recitais com bandas diversas, violão e voz e violão solo e participando de vários projetos musicais, recebendo, inclusive, troféus por ser o melhor instrumentista em festivais de música.
				</p>
				<p>
					Compõe há mais de 20 anos em diferentes linhas da música instrumental e vocal, criando mais de 1.000 composições, como estudos para violão e voz, música instrumental para violão solo e guitarra, música para bandas de vários estilos (do Sertanejo ao Rock), atuando em gravações como convidado, compondo e produzindo jingles e músicas sob encomenda.
				</p>
				<p>
					Gravou várias músicas solo e com bandas como Dheva e Hilleyes (em parceria com alguns de seus irmãos), CDs de projetos e artistas como Esmi Faraon – “Minhas Canções” / Pop / Sertanejo, Ricardo Shanti “Poesias” e “Luanda” / Poesia / MPB / Ritmos Diversos e Simone Flores – Gospel, dentre outros e atualmente compõe para artistas de diversos estilos musicais, além de trabalhar com gravações e arranjos particulares em estúdio.
				</p>
				<p>
					No ano de 2009 passou em 2º Lugar no Concurso para Professor de Artes Musicais em Esteio (RS) e iniciou em 2010 seus trabalhos naquele município. No final de 2011 teve êxito na mesma área no município de Canoas e iniciou seus trabalhos de música em 2012 nessa cidade, tendo fundado, juntamente com um colega o Núcleo de Música de Canoas, junto à Secretaria de Educação. No mesmo ano fundaram também o Núcleo de Música Intermunicipal que conta atualmente com a presença de 11 municípios da Grande Porto Alegre.
				</p>
				<p>
					Por conta dessas atividades tocou em oficinas, eventos, ministrou e organizou formação de professores, participou de encontros, seminários e shows nos quais inclusive compôs parte da trilha sonora como na “Fada que Colecionava Manhãs” da escritora Marô Barbieri (onde executou algumas composições suas) e na apresentação do escritor Gaúcho Dilan Camargo onde musicou e executou diversas poesias suas em apresentações em Canoas e na Feira do Livro de Porto Alegre (escritor com o qual ainda mantém parceria musical).
				</p>
				<p>
					Compôs também canções para projetos como o “Fome de Ler” (SME), “Meio Ambiente”, Criança A Cavalo (C.E.I.A), SPE (Saúde e Prevenção nas Escolas), momentos culturais de Oficinas de Educação Infantil e Fundamental, além de diversas obras musicais para eventos e datas comemorativas escolares.
				</p>
				<p>
					Em Março de 2013, com o apoio da direção escolar fundou o Grupo Decords G.V.M. que tem por base o repertório violonístico para grupo. Compõe material para o grupo e leciona teoria musical e técnica voltada para o ensino musical do violão.
				</p>
				<p>
					Em fevereiro de 2014 iniciou trabalho no CEIA (Centro de Educação Inclusiva e Acessibilidade), em Canoas. Atua com musicalização (canto, instrumentos de percussão, música associada às Artes Visuais, dentre vários outros) com grupos de alunos com necessidades especiais, desde Autismo, Síndrome de Down, Retardo Mental, Dificuldade de Aprendizagem, Paralisia Cerebral, dentre outros, onde trabalha atualmente.
				</p>
				<p>
					Concomitantemente ao ingresso no CEIA, iniciou o curso de Pós-Graduação em Educação Inclusiva do Unilasalle, estudando uma variedade de limitações recorrentes em alunos com necessidade especiais. Essa especialização tem previsão de término para Agosto de 2015.
				</p>
				<p>
					Em Setembro de 2014 a banda Hilleyes, na qual é compositor, vocalista, violonista e guitarrista e é composta também por seu irmão e amigos de longa data, voltou à ativa e tem tocado em diversos locais da cena Canoense.
				</p>

				<!-- Link 'Back to top' convertido para um botão elegante -->
				<div class="text-right mt-8">
					<a href="#top" class="text-blue-600 font-semibold hover:text-blue-800">
						<i class="fas fa-arrow-up mr-2"></i>Voltar ao topo
					</a>
				</div>
			</div>

		</section>

	</main>
	<!-- Footer (Padrão Decords) -->
	<footer class="bg-gray-900 text-gray-300 py-12 mt-auto">
		<div class="max-w-7xl mx-auto px-4">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-8">

				<div>
					<h3 class="text-white text-lg font-bold mb-4"><i class="fas fa-guitar mr-2"></i>DECORDS</h3>
					<p class="text-sm">Comprometidos com a educação musical de qualidade.</p>
				</div>

				<div class="text-center">
					<h4 class="text-white text-lg font-semibold mb-4">Links úteis</h4>
					<ul class="space-y-2">
						<li><a href="index.php" class="hover:text-blue-400">Início</a></li>
						<li><a href="historico.php" class="hover:text-blue-400">Histórico</a></li>
						<li><a href="professor.php" class="hover:text-blue-400">Professor</a></li>
						<li><a href="contato.php" class="hover:text-blue-400">Contato</a></li>
						<li><a href="fotos.php" class="hover:text-blue-400 transition">Fotos</a></li>
						<li><a href="videos.php" class="hover:text-blue-400 transition">Videos</a></li>
					</ul>
				</div>

				<div class="text-right">
					<h4 class="text-white font-semibold mb-4">Redes Sociais</h4>
					<div class="flex justify-end space-x-4">
						<a href="#" class="p-2 bg-gray-800 rounded-full"><i class="fab fa-facebook"></i></a>
						<a href="#" class="p-2 bg-gray-800 rounded-full"><i class="fab fa-youtube"></i></a>
						<a href="#" class="p-2 bg-gray-800 rounded-full"><i class="fab fa-blogger"></i></a>
					</div>
				</div>

			</div>
			<div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-500">
				&copy; 2016 - <?= date("Y") ?> Decords • Desenvolvido por
				<span class="text-blue-400 font-semibold">Luciano M. Rodrigues</span>
			</div>
		</div>
	</footer>

</body>

</html>