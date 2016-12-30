# -*- coding: utf-8 -*-
###############################################################################
#
#    Copyright (C) 2001-2014 Micronaet SRL (<http://www.micronaet.it>).
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as published
#    by the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
###############################################################################
import os
import sys
import logging
import openerp
import openerp.netsvc as netsvc
import openerp.addons.decimal_precision as dp
from openerp.osv import fields, osv, expression, orm
from datetime import datetime, timedelta
from dateutil.relativedelta import relativedelta
from openerp import SUPERUSER_ID, api
from openerp import tools
from openerp.tools.translate import _
from openerp.tools.float_utils import float_round as round
from openerp.tools import (DEFAULT_SERVER_DATE_FORMAT, 
    DEFAULT_SERVER_DATETIME_FORMAT, 
    DATETIME_FORMATS_MAP, 
    float_compare)


_logger = logging.getLogger(__name__)

class ProductProduct(orm.Model):
    """ Model name: ProductProduct
    """
    _inherit = 'product.product'

    def extract_web_inventory_file(self, cr, uid, context=None):
        ''' Extract and FTP publish status file
        '''        
        def clean(value):
            res = ''
            for c in value:
                if ord(c) < 127:
                    res += c
                else:
                    res += '#'
            return res
                    
        # ---------------------------------------------------------------------
        #                        XLS log export:        
        # ---------------------------------------------------------------------
        filename = '/home/administrator/photo/output/magazzino.csv'
        f_out = open(filename, 'w')

        # ---------------------------------------------------------------------
        # Populate product in correct page
        # ---------------------------------------------------------------------
        data = {
            'wizard': True, # started from wizard
            'inventory_category_id': False,#wiz_proxy.inventory_category_id.id,
            }

        for product in self.stock_status_report_get_object(
                cr, uid, data=data, context=context):
            #if product.inventory_category_id.id not in ():# TODO manage better
                
            #    continue
            f_out.write('%s|%s|%s|%s|%s|%s|%s|%s|%s|###FINERIGA###\n' % (        
                product.default_code,
                clean(product.name),
                product.mx_net_qty,
                0,
                0,
                0,
                0,
                product.lst_price,
                '',
                ))
                
        # Publish via FTP and call import document        
        return True    
        
class ResCompany(orm.Model):
    """ Model name: ResCompany
    """    
    _inherit = 'res.company'
    
    _columns = {
        'php_filename': fields.char('PHP filename', size=64),
        'php_category_ids': fields.many2many(
            'product.product.inventory.category', 'company_php_category_rel', 
            'company_id', 'category_id', 
            'Web product category', help='Inventory category published',
            ),
        }
# vim:expandtab:smartindent:tabstop=4:softtabstop=4:shiftwidth=4:
