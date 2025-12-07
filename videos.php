<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Galeria de vídeos do Projeto Decords, mostrando eventos e aulas de música.">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<title>Decords - Vídeos</title>

	<!-- Bootstrap 4 -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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

		<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="description" content="Galeria de vídeos do Projeto Decords, mostrando eventos e aulas de música."><meta name="author" content="Luciano Moraes Rodrigues"><title>Decords - Vídeos</title>< !-- Bootstrap 4 --><link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">< !-- Tailwind CSS --><script src="https://cdn.tailwindcss.com"></script>< !-- Font Awesome --><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">< !-- Google Fonts --><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet"><style>body {
			font-family: 'Inter', sans-serif;
		}
	</style>
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen pt-24">
	<!-- NAVBAR -->
	<nav class="bg-blue-900 text-white shadow-lg fixed top-0 left-0 w-full z-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex justify-between h-20 items-center">

				<!-- LOGO -->
				<a href="index.php" class="flex items-center gap-3 group">
					<img src="img/foto22.jpg"
						class="h-12 w-auto rounded-lg border-2 border-blue-400 group-hover:border-white transition object-cover">
					<span class="text-xl font-bold tracking-wide">DECORDS</span>
				</a>

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

				<!-- ÍCONES -->
				<div class="hidden md:flex items-center space-x-4 border-l border-blue-700 pl-6">
					<a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-facebook text-xl"></i></a>
					<a href="#" class="text-gray-300 hover:text-red-500"><i class="fab fa-youtube text-xl"></i></a>
					<a href="#" class="text-gray-300 hover:text-orange-400"><i class="fab fa-blogger text-xl"></i></a>
				</div>

				<!-- Mobile -->
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
				<a href="historico.php" class="block py-2 text-white">Histórico</a>
				<a href="professor.php" class="block py-2 text-white">Professor</a>
				<a href="fotos.php" class="block py-2 text-white font-bold bg-blue-700">Fotos</a>
				<a href="videos.php" class="block py-2 text-white">Vídeos</a>
				<a href="contato.php" class="block py-2 text-white">Contato</a>
				<a href="login.php" class="block py-2 text-white">Aluno</a>
				<a href="login_professor.php" class="block py-2 text-white">Professor</a>
			</div>
		</div>

	</nav>

	<!-- Espaço abaixo da navbar -->
	<div class="pt-24"></div>

	<!-- CONTEÚDO PRINCIPAL -->
	<div class="max-w-6xl mx-auto px-4 md:px-0 pb-16">

		<!-- TEMPLATE DE UM BLOCO (replicado abaixo) -->
		<?php
		function blocoVideo($descricao, $url)
		{
			echo "
        <div class='video-card'>
            <p class='font-semibold text-lg mb-4 leading-relaxed'>$descricao</p>
            <div class='embed-responsive embed-responsive-16by9'>
                <iframe class='embed-responsive-item' src='$url' allowfullscreen></iframe>
            </div>
        </div>";
		}
		?>

		<?php
		blocoVideo(
			"Vídeo Prova Brasil – Grupo Decords executa música composta pelo professor José A. Homrich com apoio da Secretaria de Educação e Comunicação de Canoas.",
			"https://www.youtube.com/embed/_BH7INrkPxc"
		);

		blocoVideo(
			"Decords Contra o Mosquito – Imagens da gravação e mobilização escolar sobre o combate ao Aedes Aegypti.",
			"https://www.youtube.com/embed/vywlPjiaRbs"
		);

		blocoVideo(
			"Ser Adolescente é Muito Mais – Apresentação no auditório do Unilasalle em 22/05/2014.",
			"https://www.youtube.com/embed/P5EHM-Qg1lw"
		);

		blocoVideo(
			"Phoenix – Ensaio divertido marcando estreia de aluna e gravação espontânea do grupo.",
			"https://www.youtube.com/embed/mFo_C0hSicM"
		);

		blocoVideo(
			"Decords – Composição de Ademir Homrich. Gravado durante a Semana Multicultural de Canoas.",
			"https://www.youtube.com/embed/pnJcZwZ8z8Y"
		);

		blocoVideo(
			"O Sol (Jota Quest) – Arranjo de Ademir Homrich, executado por integrantes do grupo.",
			"https://www.youtube.com/embed/WtIQSHhtazk"
		);

		blocoVideo(
			"Rap do Decords – Composição baseada na fala dos integrantes sobre felicidade. Show no Plaza São Rafael.",
			"https://www.youtube.com/embed/4JPsvsv14nw"
		);

		blocoVideo(
			"Ensaio num dia chuvoso – Beethoven (9ª Sinfonia) arranjo de Ademir Homrich.",
			"https://www.youtube.com/embed/gLUaoROccHk"
		);

		blocoVideo(
			"Melhores momentos do ensaio – Publicado em setembro de 2015.",
			"https://www.youtube.com/embed/U8R_4unqDSo"
		);

		blocoVideo(
			"Apresentação de Natal – Show acústico especial com participação da comunidade.",
			"https://www.youtube.com/embed/KCGXbIvDmyE"
		);
		?>

		<!-- Botão voltar ao topo -->
		<div class="text-center mt-12">
			<a href="#" class="text-blue-700 hover:underline font-semibold">Voltar ao topo ↑</a>
		</div>
	</div>
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
		</div>
		<div class="border-t border-gray-800 pt-8 text-center">
			<p class="text-gray-500 text-sm">
				&copy; 2016 - <?php echo date("Y"); ?> Decords. Todos os direitos reservados.
			</p>
			<p class="text-gray-600 text-xs mt-2 font-mono">
				Desenvolvedor: <span class="text-blue-500 font-semibold">Luciano Moraes Rodrigues</span>
			</p>
		</div>
	</footer>

	<!-- Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>