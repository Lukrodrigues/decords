<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Decords Música e Teoria - Aulas de violão e teoria musical">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<title>Decords Música e Teoria</title>

	<!-- Tailwind CSS (Estilo Moderno e Rápido) -->
	<script src="https://cdn.tailwindcss.com"></script>

	<!-- Font Awesome (Ícones) -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- Google Fonts (Tipografia Profissional) -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Inter', sans-serif;
		}

		/* --- Lógica do Parallax e Carrossel --- */
		.hero-section {
			height: 85vh;
			/* Ocupa 85% da altura da tela */
			position: relative;
			overflow: hidden;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.parallax-bg {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-size: cover;
			background-position: center;
			background-attachment: fixed;
			/* Efeito Parallax CSS Puro */
			transition: opacity 1.5s ease-in-out;
			/* Transição suave entre slides */
			z-index: -1;
		}

		/* Sombra no texto para melhor leitura sobre imagens */
		.text-shadow {
			text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
		}

		/* Dropdown Desktop: Garante que o menu apareça ao passar o mouse */
		@media (min-width: 768px) {
			.group:hover .group-hover\:block {
				display: block;
			}
		}
	</style>
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

	<!-- Navegação (Topo Fixo) -->
	<nav class="bg-blue-900 text-white shadow-lg fixed w-full z-50 transition-all duration-300" id="navbar">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex justify-between h-20 items-center">

				<!-- LOGO (Original restaurada: img/foto22.jpg) -->
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
							<a href="/login.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition">
								<i class="fas fa-user-graduate mr-2 text-blue-600"></i>Sou Aluno/Usuário
							</a>
							<a href="login_professor.php" class="block px-4 py-3 hover:bg-gray-100 transition">
								<i class="fas fa-chalkboard-teacher mr-2 text-blue-600"></i>Sou Professor
							</a>
						</div>
					</div>

					<a href="comentarios.php" class="hover:text-blue-300 font-medium transition">Comentários</a>
				</div>

				<!-- Ícones Redes Sociais (Restaurados) -->
				<div class="hidden md:flex items-center space-x-4 border-l border-blue-700 pl-6">
					<a href="https://www.facebook.com/decordsoficial/" target="_blank" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="Facebook">
						<i class="fab fa-facebook text-2xl"></i>
					</a>
					<a href="https://www.youtube.com/channel/UCYlKeJvPE7jUXpZMNaVtG1A" target="_blank" class="text-gray-300 hover:text-red-500 transition transform hover:scale-110" title="YouTube">
						<i class="fab fa-youtube text-2xl"></i>
					</a>
					<a href=" http://ademirhomrichmusica.blogspot.com.br" target="_blank" class="text-gray-300 hover:text-orange-500 transition transform hover:scale-110" title="Blogger">
						<i class="fab fa-blogger text-2xl"></i>
					</a>
				</div>

				<!-- Botão Mobile -->
				<div class="md:hidden">
					<button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-white hover:text-blue-300 focus:outline-none">
						<i class="fas fa-bars text-2xl"></i>
					</button>
				</div>
			</div>
		</div>

		<!-- Menu Mobile (Expansível) -->
		<div id="mobile-menu" class="hidden md:hidden bg-blue-800 border-t border-blue-700">
			<div class="px-4 pt-2 pb-4 space-y-2">
				<p class="text-gray-400 text-xs font-bold uppercase tracking-wider mt-2">Navegação</p>
				<a href="historico.php" class="block px-3 py-2 rounded-md hover:bg-blue-700 text-white">Histórico</a>
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

	<!-- HERO SECTION: Carrossel Parallax com Imagens Originais (foto32, foto36, foto40) -->
	<header class="hero-section">
		<!-- Slide 1: Foto 32 -->
		<div class="parallax-bg opacity-100" style="background-image: url('img/foto32.jpg');"></div>

		<!-- Slide 2: Foto 36 -->
		<div class="parallax-bg opacity-0" style="background-image: url('img/foto36.jpg');"></div>

		<!-- Slide 3: Foto 40 -->
		<div class="parallax-bg opacity-0" style="background-image: url('img/foto40.jpg');"></div>

		<!-- Overlay Escuro para melhorar leitura do texto -->
		<div class="absolute inset-0 bg-black bg-opacity-60"></div>

		<!-- Conteúdo Hero -->
		<div class="relative z-10 text-center px-4 max-w-4xl mx-auto text-white">
			<h1 class="text-4xl md:text-6xl font-extrabold mb-6 text-shadow tracking-tight" id="hero-title">Decords Música e Teoria</h1>
			<p class="text-lg md:text-2xl mb-10 text-gray-200 font-light" id="hero-desc">
				Desenvolvendo técnica e sensibilidade musical no violão através da educação.
			</p>
			<div class="flex flex-col sm:flex-row gap-4 justify-center">
				<a href="historico.php" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full transition shadow-lg transform hover:-translate-y-1">
					Conheça o Projeto
				</a>
				<a href="contato.php" class="px-8 py-3 bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-900 font-semibold rounded-full transition shadow-lg">
					Entre em Contato
				</a>
			</div>
		</div>
	</header>

	<!-- DESTAQUES: Cards com Imagens Originais (foto14, foto24, foto22) -->
	<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 bg-white -mt-10 relative z-20 rounded-t-3xl shadow-2xl md:bg-transparent md:mt-0 md:shadow-none md:rounded-none">

		<div class="text-center mb-16 md:mt-10">
			<h2 class="text-3xl font-bold text-gray-800">Destaques do Portal</h2>
			<div class="w-20 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-10">

			<!-- Card Histórico (Foto 14) -->
			<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 group">
				<div class="h-56 overflow-hidden relative">
					<div class="absolute inset-0 bg-blue-900 opacity-0 group-hover:opacity-20 transition duration-300 z-10"></div>
					<img src="img/foto14.jpg" alt="Histórico" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
				</div>
				<div class="p-8">
					<h3 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition">Histórico</h3>
					<p class="text-gray-600 mb-6 text-sm leading-relaxed text-justify">
						O projeto tem como público-alvo os estudantes da Escola Ministro Rubem Carlos Ludwig. Desenvolve o Ensino Musical através do Violão, objetivando contribuir para inserção da música na escola com qualidade.
					</p>
					<a href="historico.php" class="inline-flex items-center text-blue-600 font-bold hover:text-blue-800 transition">
						Ver Detalhes <i class="fas fa-arrow-right ml-2"></i>
					</a>
				</div>
			</div>

			<!-- Card Professor (Foto 24) -->
			<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 group">
				<div class="h-56 overflow-hidden relative">
					<div class="absolute inset-0 bg-blue-900 opacity-0 group-hover:opacity-20 transition duration-300 z-10"></div>
					<img src="img/foto24.jpg" alt="Professor" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
				</div>
				<div class="p-8">
					<h3 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition">Professor</h3>
					<p class="text-gray-600 mb-6 text-sm leading-relaxed text-justify">
						Conduzido pelo Professor José A. Homrich, Licenciado em Música pela UFRGS. Músico, professor, educador, instrumentista, cantor e compositor. Saiba mais sobre sua trajetória.
					</p>
					<a href="professor.php" class="inline-flex items-center text-blue-600 font-bold hover:text-blue-800 transition">
						Ver Detalhes <i class="fas fa-arrow-right ml-2"></i>
					</a>
				</div>
			</div>

			<!-- Card Exercícios (Foto 22 - Reutilizada conforme original) -->
			<div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-2xl transition duration-300 transform hover:-translate-y-2 group">
				<div class="h-56 overflow-hidden relative">
					<div class="absolute inset-0 bg-blue-900 opacity-0 group-hover:opacity-20 transition duration-300 z-10"></div>
					<img src="img/foto22.jpg" alt="Exercícios" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
				</div>
				<div class="p-8">
					<h3 class="text-2xl font-bold mb-3 text-gray-800 group-hover:text-blue-600 transition">Exercícios</h3>
					<p class="text-gray-600 mb-6 text-sm leading-relaxed text-justify">
						Área dedicada ao aprendizado. Utilize para entendimento dos tutoriais, provas anteriores e para conhecer um pouco de teoria musical aplicada ao violão.
					</p>
					<a href="login.php" class="inline-flex items-center text-blue-600 font-bold hover:text-blue-800 transition">
						Ver Detalhes <i class="fas fa-arrow-right ml-2"></i>
					</a>
				</div>
			</div>

		</div>
	</main>

	<!-- ROADMAP: Navegação Rápida (Estilo Mapa) -->
	<section class="bg-gray-100 py-16 border-t border-gray-200">
		<div class="max-w-5xl mx-auto px-4">
			<div class="text-center mb-12">
				<h2 class="text-3xl font-bold text-gray-800">Mapa de Navegação</h2>
				<div class="w-20 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
				<p class="text-gray-500 mt-2">Caminho rápido para o conhecimento</p>
			</div>

			<div class="relative">
				<!-- Linha Conectora (Desktop) -->
				<div class="hidden md:block absolute top-1/2 left-0 w-full h-1 bg-gray-300 -translate-y-1/2 z-0"></div>

				<div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">

					<!-- Passo 1 -->
					<div class="bg-white p-6 rounded-lg shadow-md text-center cursor-pointer hover:shadow-xl transition transform hover:-translate-y-1" onclick="window.location='historico.php'">
						<div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold border-4 border-white shadow">1</div>
						<h4 class="text-lg font-bold text-gray-800">Institucional</h4>
						<p class="text-sm text-gray-500 mt-1">Nossa História</p>
					</div>

					<!-- Passo 2 -->
					<div class="bg-white p-6 rounded-lg shadow-md text-center cursor-pointer hover:shadow-xl transition transform hover:-translate-y-1" onclick="window.location='professor.php'">
						<div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold border-4 border-white shadow">2</div>
						<h4 class="text-lg font-bold text-gray-800">O Mestre</h4>
						<p class="text-sm text-gray-500 mt-1">Conheça o Professor</p>
					</div>

					<!-- Passo 3 -->
					<div class="bg-white p-6 rounded-lg shadow-md text-center cursor-pointer hover:shadow-xl transition transform hover:-translate-y-1" onclick="window.location='login.php'">
						<div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-bold border-4 border-white shadow">3</div>
						<h4 class="text-lg font-bold text-gray-800">Prática</h4>
						<p class="text-sm text-gray-500 mt-1">Exercícios e Aulas</p>
					</div>

				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="bg-gray-900 text-gray-300 py-12 mt-auto">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">

				<!-- Coluna 1 -->
				<div>
					<h3 class="text-white text-lg font-bold mb-4 flex items-center">
						<i class="fas fa-guitar mr-2"></i> DECORDS
					</h3>
					<p class="text-sm leading-relaxed text-gray-400">
						Comprometidos com a educação musical de qualidade, formando não apenas músicos, mas cidadãos através da arte.
					</p>
				</div>

				<!-- Coluna 2 -->
				<div class="md:text-center">
					<h4 class="text-white text-lg font-semibold mb-4">Links Rápidos</h4>
					<ul class="space-y-2 text-sm">
						<li><a href="index.php" class="hover:text-blue-400 transition">Início</a></li>
						<li><a href="historico.php" class="hover:text-blue-400 transition">Histórico</a></li>
						<li><a href="professor.php" class="hover:text-blue-400 transition">Professor</a></li>
						<li><a href="contato.php" class="hover:text-blue-400 transition">Contato</a></li>
						<li><a href="fotos.php" class="hover:text-blue-400 transition">Fotos</a></li>
						<li><a href="videos.php" class="hover:text-blue-400 transition">Videos</a></li>


					</ul>
				</div>

				<!-- Coluna 3 -->
				<div class="md:text-right">
					<h4 class="text-white text-lg font-semibold mb-4">Redes Sociais</h4>
					<div class="flex md:justify-end space-x-4">
						<a href="https://www.facebook.com/decordsoficial/" class="bg-gray-800 p-2 rounded-full hover:bg-blue-600 transition text-white"><i class="fab fa-facebook-f"></i></a>
						<a href="https://www.youtube.com/channel/UCYlKeJvPE7jUXpZMNaVtG1A" class="bg-gray-800 p-2 rounded-full hover:bg-red-600 transition text-white"><i class="fab fa-youtube"></i></a>
						<a href=" http://ademirhomrichmusica.blogspot.com.br" class="bg-gray-800 p-2 rounded-full hover:bg-orange-600 transition text-white"><i class="fab fa-blogger"></i></a>
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

	<!-- Lógica do Carrossel (JavaScript Puro) -->
	<script>
		const slides = document.querySelectorAll('.parallax-bg');
		const heroTitle = document.getElementById('hero-title');
		const heroDesc = document.getElementById('hero-desc');
		let currentSlide = 0;

		// Conteúdo de texto que acompanha cada slide
		const slideContent = [{
				title: "Decords Música e Teoria",
				desc: "Desenvolvendo técnica e sensibilidade musical no violão através da educação."
			},
			{
				title: "Aprenda com Mestres",
				desc: "Metodologias aprovadas e ensino focado no desenvolvimento do aluno."
			},
			{
				title: "Pratique e Evolua",
				desc: "Acesso exclusivo a exercícios e materiais de apoio."
			}
		];

		function nextSlide() {
			// Oculta slide atual
			slides[currentSlide].style.opacity = 0;

			// Avança índice (loop)
			currentSlide = (currentSlide + 1) % slides.length;

			// Mostra próximo slide
			slides[currentSlide].style.opacity = 1;

			// Efeito suave no texto
			heroTitle.style.opacity = 0;
			heroDesc.style.opacity = 0;

			setTimeout(() => {
				heroTitle.innerText = slideContent[currentSlide].title;
				heroDesc.innerText = slideContent[currentSlide].desc;
				heroTitle.style.opacity = 1;
				heroDesc.style.opacity = 1;
			}, 500); // Sincroniza com a transição do texto
		}

		// Troca de slide a cada 5 segundos
		setInterval(nextSlide, 5000);
	</script>
</body>

</html>