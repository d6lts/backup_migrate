
Drupal.backup_migrate = {
  callbackURL : "",  
  autoAttach  : function() {
    if (Drupal.settings.backup_migrate !== undefined) { 
      if (Drupal.settings.backup_migrate.dependents !== undefined) { 
        for (key in Drupal.settings.backup_migrate.dependents) {
          info = Drupal.settings.backup_migrate.dependents[key];
          dependent = $('#edit-' + info['dependent']);
          for (key in info['dependencies']) {
            $('[name="' + key + '"]').each(function() {
              var dependentval = info['dependencies'][key];
              var dependency = $(this);
              (function(dependent, dependency) {
                var checkval = function(inval) {
                  if (dependency.attr('type') == 'radio') {
                    var val = $('[name="' + dependency.attr('name') + '"]:checked').val();
                    return val == inval;
                  }
                  else if (dependency.attr('type') == 'checkbox') {
                    return dependency.attr('checked') && inval == dependency.val();
                  }
                  else {
                    return dependency.val() == inval;
                  }
                  return false;
                };
                if (!checkval(dependentval)) {
                  // Hide doesn't work inside collapsed fieldsets.
                  dependent.css('display', 'none');
                }
                dependency.bind('load change click keypress focus', function() {
                  if (checkval(dependentval)) {
                    dependent.slideDown();
                  }
                  else {
                    dependent.slideUp();
                  }
                });
              })(dependent, dependency);
            });
          }
        }
      }

      $('#edit-filters-exclude-tables').after('<div class="description backup-migrate-checkbox-link"><a href="javascript:Drupal.backup_migrate.selectToCheckboxes(\''+ 'exclude_tables' +'\');">'+ Drupal.settings.backup_migrate.checkboxLinkText +'</a></div>');
      $('#edit-filters-nodata-tables').after('<div class="description backup-migrate-checkbox-link"><a href="javascript:Drupal.backup_migrate.selectToCheckboxes(\''+ 'nodata_tables' +'\');">'+ Drupal.settings.backup_migrate.checkboxLinkText +'</a></div>');
    }
  },

  processCheckboxes : function(ctxt) {
    $("input.backup-migrate-tables-checkbox", ctxt).each(function() {
      this.do_click = function() {
        if (this.checked) {
          $(this).parent().addClass('checked');
        }
        else {
          $(this).parent().removeClass('checked');
        }
      };
      $(this).bind("click", function() { this.do_click() });
      this.do_click();
    });
  },

  selectToCheckboxes : function(field) {
    var field_id = 'edit-filters-'+ field.replace('_', '-') ;
    var $select = $('#'+ field_id);
    var $checkboxes = $('<div></div>').addClass('backup-migrate-tables-checkboxes');
    $('option', $select).each(function(i) {
      $checkboxes.append('<div class="form-item"><label class="option backup-migrate-table-select"><input type="checkbox" class="backup-migrate-tables-checkbox" id="edit-'+ field_id +'-'+ this.value +'" name="'+ $select.attr('name') +'"'+ (this.selected ? 'checked="checked"' : '') +' value="'+ this.value +'"/>'+this.value+'</label></div>');
    });
    $select.parent().find('.backup-migrate-checkbox-link').remove();
    $select.before($checkboxes);
    $select.hide();
    Drupal.backup_migrate.processCheckboxes($checkboxes);
  }
}

// Global Killswitch
if (Drupal.jsEnabled) {
  $(document).ready(Drupal.backup_migrate.autoAttach);
}
