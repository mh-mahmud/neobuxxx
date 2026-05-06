                        <?php if( $this->webspice->message_board(null, 'get') ): ?>
                        <div id="message_board" class="alert alert-success">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <!-- <h4>Success</h4> -->
                            <!-- <p style="font-size: 14px">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt.</p> -->
                            <p><?php echo $this->webspice->message_board(null,'get_and_destroy'); ?></p>
                        </div>
                        <?php endif; ?>