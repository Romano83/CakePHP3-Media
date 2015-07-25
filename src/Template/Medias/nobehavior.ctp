<div class="block">
	<div class="content">
		<h1><?= __d('media', 'Error'); ?></h1>
		<p><?= __d('media', "Table '{0}Table' doesn't have 'Media' behavior", $ref); ?></p>
		<pre>
			class <?= $ref; ?>Table extends Table{
			
				public function initialize(array $config){
					$this->addBehavior('Media.Media');
				}
				
			}
		</pre>
	</div>
</div>