<!DOCTYPE html>
<html lang="en">
	<head>
		<title>three.js webgl</title>
		<meta charset="utf-8">
		
		<style>
			body {
				color: #888888;
				font-family:Monospace;
				font-size:13px;
				background-color: #000;
				margin: 0px;
				overflow: hidden;
			}
			#info {
				position: absolute;
				top: 0px;
				width: 400px;
				left: calc(50% - 200px);
				text-align: center;
			}
			a {
				color: #00f;
			}
		</style>
	</head>
	<body>

		<div id="container"></div>

		<script src="js/three.min.js"></script>
		<script src="https://threejs.org/examples/js/controls/OrbitControls.js"></script>		
		
		<script src="https://threejs.org/examples/js/objects/Reflector.js"></script>
		<script src="https://threejs.org/examples/js/objects/ReflectorRTT.js"></script>
		<script src="https://threejs.org/examples/js/controls/OrbitControls.js"></script>

		<script type="module">
			import 'https://threejs.org/examples/js/nodes/THREE.Nodes.js';
			import 'https://threejs.org/examples/js/loaders/NodeMaterialLoader.js';
			// scene size
			var WIDTH = window.innerWidth;
			var HEIGHT = window.innerHeight;
			// camera
			var VIEW_ANGLE = 45;
			var ASPECT = WIDTH / HEIGHT;
			var NEAR = 1;
			var FAR = 500;

			var camera, scene, renderer;
			var clock = new THREE.Clock();
			var cameraControls;
			
			var sphereGroup, smallSphere;
			var rightMirrorMaterial;
			var frame = new THREE.NodeFrame();
			function init() {
				// renderer
				
				var canvas = document.createElement( 'canvas' );
				var context = canvas.getContext( 'webgl2', { antialias: false } );
				renderer = new THREE.WebGLRenderer( { canvas: canvas, context: context, preserveDrawingBuffer: true, } );				
				//renderer = new THREE.WebGLRenderer();
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( WIDTH, HEIGHT );
				// scene
				scene = new THREE.Scene();
				// camera
				camera = new THREE.PerspectiveCamera( VIEW_ANGLE, ASPECT, NEAR, FAR );
				camera.position.set( 0, 75, 160 );
				cameraControls = new THREE.OrbitControls( camera, renderer.domElement );
				cameraControls.target.set( 0, 40, 0 );
				cameraControls.maxDistance = 400;
				cameraControls.minDistance = 10;
				cameraControls.update();
				//var container = document.getElementById( 'container' );
				//container.appendChild( renderer.domElement );
				document.body.appendChild( renderer.domElement );
			}
			function fillScene() {
				var planeGeo = new THREE.PlaneBufferGeometry( 100.1, 100.1 );
				// reflector/mirror plane
				
				var mirrorRTT = new THREE.ReflectorRTT(new THREE.PlaneBufferGeometry( 100, 100 ), {clipBias: 0.003, textureWidth:WIDTH, textureHeight:HEIGHT});
				
				var mirrorRTT_bottom = new THREE.ReflectorRTT(new THREE.PlaneBufferGeometry( 100, 100 ), {clipBias: 0.003, textureWidth:WIDTH, textureHeight:HEIGHT});
				var mirrorRTT_right = new THREE.ReflectorRTT(new THREE.PlaneBufferGeometry( 100, 100 ), {clipBias: 0.003, textureWidth:WIDTH, textureHeight:HEIGHT});
				
				mirrorRTT_right.position.x = 50;
				mirrorRTT_right.position.y = 50;
				mirrorRTT_right.rotateY( - Math.PI / 2 );
				scene.add( mirrorRTT_right );
				
				
				mirrorRTT_bottom.position.x = 0;
				mirrorRTT_bottom.position.y = 0;
				mirrorRTT_bottom.rotateX( - Math.PI / 2 );
				scene.add( mirrorRTT_bottom );
				
				
				rightMirrorMaterial = new THREE.PhongNodeMaterial();
				rightMirrorMaterial.color.r = 0; 
				rightMirrorMaterial.color.g = 0; 
				rightMirrorMaterial.color.b = 0; 
				rightMirrorMaterial.environment = new THREE.ReflectorNode( mirrorRTT_right ); 
				//rightMirrorMaterial.defaultAttributeValues.color = [0.5, 0.5, 0.5];
					console.log(rightMirrorMaterial);			
				
				
				var groundMirrorMaterial_2 = new THREE.PhongNodeMaterial();
				groundMirrorMaterial_2.environment = new THREE.ReflectorNode( mirrorRTT_bottom ); 				
				
				
				var mirrorMesh_2 = new THREE.Mesh( planeGeo, groundMirrorMaterial_2 );
				mirrorRTT_bottom.add( mirrorMesh_2 );
				
				
				

				sphereGroup = new THREE.Object3D();
				scene.add( sphereGroup );
				
				var geometry = new THREE.CylinderBufferGeometry( 0.1, 15 * Math.cos( Math.PI / 180 * 30 ), 0.1, 24, 1 );
				var material = new THREE.MeshPhongMaterial( { color: 0xffffff, emissive: 0x444444 } );
				var sphereCap = new THREE.Mesh( geometry, material );
				sphereCap.position.y = - 15 * Math.sin( Math.PI / 180 * 30 ) - 0.05;
				sphereCap.rotateX( - Math.PI );
				var geometry = new THREE.SphereBufferGeometry( 15, 24, 24, Math.PI / 2, Math.PI * 2, 0, Math.PI / 180 * 120 );
				var halfSphere = new THREE.Mesh( geometry, material );
				halfSphere.add( sphereCap );
				halfSphere.rotateX( - Math.PI / 180 * 135 );
				halfSphere.rotateZ( - Math.PI / 180 * 20 );
				halfSphere.position.y = 7.5 + 15 * Math.sin( Math.PI / 180 * 30 );
				sphereGroup.add( halfSphere );
				
				var geometry = new THREE.IcosahedronBufferGeometry( 5, 0 );
				var material = new THREE.MeshPhongMaterial( { color: 0xffffff, emissive: 0x333333, flatShading: true } );
				smallSphere = new THREE.Mesh( geometry, material );
				scene.add( smallSphere );
				
				// walls
				var planeTop = new THREE.Mesh( planeGeo, new THREE.MeshPhongMaterial( { color: 0xffffff } ) );
				planeTop.position.y = 100;
				planeTop.rotateX( Math.PI / 2 );
				scene.add( planeTop );
				var planeBack = new THREE.Mesh( planeGeo, new THREE.MeshPhongMaterial( { color: 0xffffff } ) );
				planeBack.position.z = - 50;
				planeBack.position.y = 50;
				scene.add( planeBack );
				var planeFront = new THREE.Mesh( planeGeo, new THREE.MeshPhongMaterial( { color: 0x7f7fff } ) );
				planeFront.position.z = 50;
				planeFront.position.y = 50;
				planeFront.rotateY( Math.PI );
				scene.add( planeFront );
				var planeRight = new THREE.Mesh( planeGeo, rightMirrorMaterial );
				planeRight.position.x = 50;
				planeRight.position.y = 50;
				planeRight.rotateY( - Math.PI / 2 );
				scene.add( planeRight );
				var planeLeft = new THREE.Mesh( planeGeo, new THREE.MeshPhongMaterial( { color: 0xff0000 } ) );
				planeLeft.position.x = - 50;
				planeLeft.position.y = 50;
				planeLeft.rotateY( Math.PI / 2 );
				scene.add( planeLeft );
				// lights
				var mainLight = new THREE.PointLight( 0xcccccc, 1.5, 250 );
				mainLight.position.y = 60;
				scene.add( mainLight );
				var greenLight = new THREE.PointLight( 0x00ff00, 0.25, 1000 );
				greenLight.position.set( 550, 50, 0 );
				scene.add( greenLight );
				var redLight = new THREE.PointLight( 0xff0000, 0.25, 1000 );
				redLight.position.set( - 550, 50, 0 );
				scene.add( redLight );
				var blueLight = new THREE.PointLight( 0x7f7fff, 0.25, 1000 );
				blueLight.position.set( 0, 50, 550 );
				scene.add( blueLight );
			}
			
			
			function update() {
				requestAnimationFrame( update );
				
				var timer = Date.now() * 0.01;
				sphereGroup.rotation.y -= 0.002;
				smallSphere.position.set(
					Math.cos( timer * 0.1 ) * 30,
					Math.abs( Math.cos( timer * 0.2 ) ) * 20 + 5,
					Math.sin( timer * 0.1 ) * 30
				);
				smallSphere.rotation.y = ( Math.PI / 2 ) - timer * 0.1;
				smallSphere.rotation.z = timer * 0.8;
				//frame.update( delta ).updateNode( rightMirrorMaterial );
				renderer.render( scene, camera );
			}
			init();
			fillScene();
			update();
		</script>
	</body>
</html>