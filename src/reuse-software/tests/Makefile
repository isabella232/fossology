# SPDX-License-Identifier: GPL-2.0
# SPDX-FileCopyrightText: Copyright (c) 2021 Orange 
# Author: Bartłomiej Dróżdż <bartlomiej.drozdz@orange.com>
# 
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# version 2 as published by the Free Software Foundation.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

TOP = ../../..
VARS = $(TOP)/Makefile.conf
include $(VARS)

DIRS =  tests
TESTDIR = tests

dirloop=for dir in $(DIRS); do $(MAKE) -s -C $$dir $(1);  done

all:
	echo "all: TEST: Nothing to do .."

test:
	echo "test: Nothing to do .."

coverage:
	echo "Coverage: Nothing to do .."

install: all
	echo "Install: nothing to do .."

uninstall:
	echo "Uninstall: nothing to do .."

clean:
	echo "clean: Nothing to do .."

.PHONY: all install uninstall clean test