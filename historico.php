<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Histórico do Projeto Decords Música e Teoria.">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<title>Decords - Histórico</title>

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

		/* Dropdown Desktop */
		@media (min-width: 768px) {
			.group:hover .group-hover\:block {
				display: block;
			}
		}

		/* Parágrafos do histórico */
		#historico-content p {
			text-indent: 50px;
			text-align: justify;
			margin-bottom: 1.5rem;
			line-height: 1.75;
		}
	</style>
</head>

<!-- BODY SEM PT-20 (CORRIGIDO) -->

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

	<!-- NAVBAR FIXA E COLADA NO TOPO -->
	<nav class="bg-blue-900 text-white shadow-lg fixed top-0 left-0 w-full z-50 transition-all duration-300" id="navbar">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex justify-between h-20 items-center">

				<!-- LOGO -->
				<div class="flex-shrink-0 flex items-center gap-3">
					<a href="index.php" class="flex items-center gap-3 group">
						<img src="img/foto22.jpg"
							alt="Decords Logo"
							class="h-12 w-auto rounded-lg border-2 border-blue-400 group-hover:border-white transition object-cover">
						<span class="text-xl font-bold tracking-wide">DECORDS</span>
					</a>
				</div>

				<!-- Menu Desktop -->
				<div class="hidden md:flex items-center space-x-6">

					<!-- Dropdown Categorias -->
					<div class="relative group h-full py-6 cursor-pointer">
						<span class="hover:text-blue-300 font-medium flex items-center transition">
							Categorias <i class="fas fa-chevron-down ml-1 text-xs"></i>
						</span>

						<div class="absolute top-16 left-0 w-48 bg-white text-gray-800 shadow-xl rounded-b-md hidden group-hover:block border-t-4 border-blue-500">
							<a href="historico.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition font-bold text-blue-600">
								<i class="fas fa-history mr-2 text-blue-500"></i>Histórico
							</a>
							<a href="professor.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition">
								<i class="fas fa-chalkboard-teacher mr-2 text-blue-500"></i>Professor
							</a>
							<a href="fotos.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition">
								<i class="fas fa-images mr-2 text-blue-500"></i>Fotos
							</a>
							<a href="videos.php" class="block px-4 py-3 hover:bg-gray-100 transition">
								<i class="fas fa-video mr-2 text-blue-500"></i>Vídeos
							</a>
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

				<!-- Ícones Redes -->
				<div class="hidden md:flex items-center space-x-4 border-l border-blue-700 pl-6">
					<a href="https://www.facebook.com/decordsoficial/" target="_blank" class="text-gray-300 hover:text-white transition transform hover:scale-110">
						<i class="fab fa-facebook text-2xl"></i>
					</a>
					<a href="https://www.youtube.com/channel/UCYlKeJvPE7jUXpZMNaVtG1A" target="_blank" class="text-gray-300 hover:text-red-500 transition transform hover:scale-110">
						<i class="fab fa-youtube text-2xl"></i>
					</a>
					<a href="http://ademirhomrichmusica.blogspot.com.br" target="_blank" class="text-gray-300 hover:text-orange-500 transition transform hover:scale-110">
						<i class="fab fa-blogger text-2xl"></i>
					</a>
				</div>

				<!-- Mobile Button -->
				<div class="md:hidden">
					<button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-white hover:text-blue-300">
						<i class="fas fa-bars text-2xl"></i>
					</button>
				</div>

			</div>
		</div>

		<!-- Menu Mobile -->
		<div id="mobile-menu" class="hidden md:hidden bg-blue-800 border-t border-blue-700">
			<div class="px-4 pt-2 pb-4 space-y-2">

				<p class="text-gray-400 text-xs font-bold uppercase tracking-wider mt-2">Navegação</p>

				<a href="historico.php" class="block px-3 py-2 rounded-md bg-blue-700 text-white font-bold">Histórico</a>
				<a href="professor.php" class="block px-3 py-2 rounded-md hover:bg-blue-700 text-white">Professor</a>
				<a href="fotos.php" class="block px-3 py-2 rounded-md hover:bg-blue-700 text-white">Fotos</a>
				<a href="videos.php" class="block px-3 py-2 rounded-md hover:bg-blue-700 text-white">Vídeos</a>
				<a href="contato.php" class="block px-3 py-2 rounded-md hover:bg-blue-700 text-white">Contato/Agenda</a>
				<a href="comentarios.php" class="block px-3 py-2 rounded-md hover:bg-blue-700 text-white">Comentários</a>

				<p class="text-gray-400 text-xs font-bold uppercase tracking-wider mt-4">Acesso</p>

				<a href="login.php" class="block px-3 py-2 rounded-md hover:bg-blue-700 text-white">Área do Aluno</a>
				<a href="login_professor.php" class="block px-3 py-2 rounded-md hover:bg-blue-700 text-white">Área do Professor</a>

			</div>
		</div>

	</nav>

	<!-- CONTEÚDO PRINCIPAL -->
	<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-10 md:pt-28 md:pb-16 flex-grow">

		<header class="text-center mb-12">
			<h1 class="text-4xl font-extrabold text-blue-900 mb-2">O Projeto Decords</h1>
			<h2 class="text-xl font-medium text-gray-600">Nossa História e Evolução</h2>
			<div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
		</header>

		<section class="grid grid-cols-1 md:grid-cols-12 gap-8" id="historico-content">

			<!-- Imagem -->
			<div class="md:col-span-4">
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto14.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto35.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto28.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto16.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto11.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto8.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';"><br>
				<img class="w-full h-auto rounded-lg shadow-xl border-4 border-gray-200"
					src="img/foto7.jpg"
					alt="Foto"
					onerror="this.onerror=null;this.src='https://placehold.co/410x340/0000FF/FFFFFF?text=Imagem+Nao+Encontrada';">
			</div>




			<!-- Textos -->
			<!-- Textos do Histórico -->
			<div class="md:col-span-8 lg:col-span-8 text-lg text-gray-700">
				<p>
					O Projeto dos Grupos de Violão da Escola Ministro Rubem Carlos Ludwig é conduzido pelo Professor de Música José A. Homrich – Formado em Licenciatura em Música pela UFRGS e Pós-Graduando do Curso de Educação Inclusiva.
					Surgiu após o primeiro chamamento de professores de Música para o município de Canoas.
					Conforme a Lei Nº 11.769 de Agosto de 2008 a inserção da música nas escolas é obrigatório,
					tendo como último prazo o início do Ano de 2012. Dessa forma o Professor José A. Homrich (Licenciado em Música pela UFRGS)
					iniciou seus trabalhos com música na Escola Ministro Ludwig a partir do mês de Julho de 2012.
				</p>
				<p>
					Sendo uma disciplina nova no currículo das escolas e tendo obrigatoriedade nas escolas,
					logo percebeu-se diversas possibilidades para que essa inserção fosse com boa qualidade,
					mesmo tendo pouco material didático especializado produzido. Sendo Licenciado em Música e
					tendo habilitação e larga experiência em instrumentos de corda (Violão, Guitarra e Canto) e
					canto, logo ficou evidenciado que em turmas com grande número de alunos e ainda não tendo
					qualquer instrumento musical a disposição seria inviável atender ao apelo dos alunos que
					gostariam, segundo os diagnósticos aplicados pelo professor com as turmas no começo de cada ano
					de trabalho, a aprendizagem mais focada a um instrumento musical específico seria menos
					inviável. Tendo em vista as habilidades musicais com instrumento de corda do professor
					começou a surgir a idéia da implantação de grupos de violão.
				</P>
				<p>
					O projeto foi idealizado no ano de 2012 quando o professor José Homrich – primeiro professor de Música do município de Canoas,
					juntamente com o professor Alexandre Rodrigues - iniciou seus trabalhos musicais na disciplina de Artes na
					escola Ministro Rubem Carlos Ludwig. Até aquele momento a base dos planos de estudos da escola em questão
					e do município era as Artes Visuais.
				</p>
				<p>
					Após o início das aulas de Artes voltadas para a música percebeu-se uma grande dificuldade:
					por conta das turmas serem numerosas e as salas pequenas e de não haverem instrumentos musicais
					disponíveis, percebeu-se que, mesmo se houvesse a possibilidade de adquirir tais instrumentos
					não haveria espaço físico para utilizá-los. Inicialmente o professor José, em combinação
					com a Diretora Letícia analisou a possibilidade de formar alguns grupos de instrumentistas
					num turno inverso, porém, em virtude da requisição do professor para trabalhar juntamente
					com Secretaria de Educação do município (SME) não houve a possibilidade naquele momento da
					conclusão do projeto original.
				</p>
				<p>
					No início do ano seguinte a diretora da escola manteve contato com a Secretaria de Educação e conseguiu a
					liberação do professor José para uma manhã de trabalho na escola com o Projeto de Grupo de Violões.
					Após a confirmação de que seria viável a construção do Projeto, o professor fez orçamentos,
					pesquisou preços de violões acústicos e elétricos, além de acessórios para aula de música e,
					através de verbas da própria escola foram adquiridos e o projeto tomou forma, iniciando em Março de 2013.
				</p>
				<p>
					Inicialmente foram feitos convites aos alunos para a aula de instrumento na própria sala de aula e
					dessa forma pensou-se em dois grupos de violão: um para trabalhar conceitos e iniciação musical através
					do violão com um repertório mais simplificado e outro grupo que já praticava violão a um determinado tempo
					e que teria um repertório violonístico mais elaborado. O segundo grupo que era menor e composto por alunos
					que já tinham contato com o instrumento em poucas semanas já estava apresentando-se na escola, durante a
					celebração da Páscoa (música composta pelo Professor José). Um dos alunos que apresentou-se,
					visivelmente emocionado agradeceu pela oportunidade de estar ali, fazendo música e tocando para um grande público
					(formado por alunos da escola e seus familiares). Após menos de dois meses de aprendizagem musical ao violão
					o Grupo de Violões contando com 16 instrumentos (seis deles pertencentes à escola e três pertencentes ao professor)
					apresentou-se nas comemorações do Dia das Mães da Escola Ministro Rubem Carlos Ludwig (novamente com música composta pelo Professor José).
				</p>
				<p>
					Atualmente o Grupo tem aproximadamente 50 integrantes dentre violonistas e alguns cantores de um grupo vocal formado em 2015.
					São atendidos alunos da escola e nos shows que o grupo atua como convidado, recebe apoio instrumental de alguns alunos que já se formaram na escola,
					mas que mantém o vínculo com a escola por residirem na comunidade escolar e terem laços de amizade com alunos e professores. O grupo tem se dedicado
					ao estudo da parte técnica do instrumento e montado repertório a partir de composições próprias e de músicas Nacionais e Internacionais de
					diversos estilos e gêneros para apresentações musicais.
				</p>
				<div class="text-right mt-8">
					<a href="#top" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800">
						<i class="fas fa-arrow-up mr-2"></i> Voltar ao Topo
					</a>
				</div>

			</div>
		</section>

	</main>

	<!-- FOOTER -->
	<footer class="bg-gray-900 text-gray-300 py-12 mt-auto">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

			<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">

				<div>
					<h3 class="text-white text-lg font-bold mb-4"><i class="fas fa-guitar mr-2"></i> DECORDS</h3>
					<p class="text-sm text-gray-400">Comprometidos com a educação musical de qualidade.</p>
				</div>

				<div class="md:text-center">
					<h4 class="text-white text-lg font-semibold mb-4">Links Rápidos</h4>
					<ul class="space-y-2 text-sm">
						<li><a href="index.php" class="hover:text-blue-400">Início</a></li>
						<li><a href="historico.php" class="hover:text-blue-400">Histórico</a></li>
						<li><a href="professor.php" class="hover:text-blue-400">Professor</a></li>
						<li><a href="contato.php" class="hover:text-blue-400">Contato</a></li>
						<li><a href="fotos.php" class="hover:text-blue-400 transition">Fotos</a></li>
						<li><a href="videos.php" class="hover:text-blue-400 transition">Videos</a></li>
					</ul>
				</div>

				<div class="md:text-right">
					<h4 class="text-white text-lg font-semibold mb-4">Redes Sociais</h4>
					<div class="flex md:justify-end space-x-4">
						<a href="#" class="bg-gray-800 p-2 rounded-full hover:bg-blue-600"><i class="fab fa-facebook-f"></i></a>
						<a href="#" class="bg-gray-800 p-2 rounded-full hover:bg-red-600"><i class="fab fa-youtube"></i></a>
						<a href="#" class="bg-gray-800 p-2 rounded-full hover:bg-orange-600"><i class="fab fa-blogger"></i></a>
					</div>
				</div>

			</div>

			<div class="border-t border-gray-800 pt-8 text-center">
				<p class="text-gray-500 text-sm">
					&copy; 2016 - <?php echo date("Y"); ?> Decords. Todos os direitos reservados.
				</p>
				<p class="text-gray-600 text-xs mt-2 font-mono">
					Desenvolvedor: <span class="text-blue-500 font-semibold">Luciano Moraes Rodrigues</span>
				</p>
			</div>

		</div>
	</footer>

</body>

</html>