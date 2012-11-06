<div class="accordion">
  <ul>
    <li> <a href="#"><span>Sales</span></a></li>
  </ul>
  <ul>
    <li><?php echo $this->Html->link('Estimates', array('plugin' => 'estimates', 'controller' => 'estimates', 'action' => 'index')); ?></li>
    <li><?php echo $this->Html->link('Invoices', array('plugin' => 'invoices', 'controller' => 'invoices', 'action' => 'index')); ?></li>
    <li><?php echo $this->Html->link('Transactions', array('plugin' => 'transactions', 'controller' => 'transactions', 'action' => 'index')); ?></li>
  </ul>
</div>