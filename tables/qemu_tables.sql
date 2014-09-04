create database if not exists apidb;

use apidb;

drop table if exists qemuSystems;
drop table if exists qemuSoundCards;
drop table if exists qemuVGAs;

/*
 * QEMU System Emulations
 */    
create table qemuSystems (
	qsysId           int not null auto_increment,
	qsysName	        varchar(64) not null,
	qsysDescription  text,
	qsysParent       int default 0,
	key(qsysId)
);

/*
 * QEMU's emulated Sound Cards // BITMASK!!!
 */    
create table qemuSoundCards (
	qsndId           int not null,
	qsndName	        varchar(64) not null,
	qsndDescription  text,
	key(qsndId)
);

/*
 * QEMU's emulated VGA Cards
 */    
create table qemuVGAs (
	qvgaId           int not null,
	qvgaName	        varchar(64) not null,
	qvgaDescription  text,
	key(qvgaId)
);

/* STARTS qemuSystem INSERTs */
INSERT INTO qemuSystems VALUES ('0', 'MAIN', 'Root system', NULL);
UPDATE qemuSystems SET qsysId = '0' WHERE qsysName = 'MAIN' LIMIT 1;
/* Processor emulators */
INSERT INTO qemuSystems VALUES (1, 'alpha', 'QEMU Alpha System Emulation', 0);
INSERT INTO qemuSystems VALUES (2, 'arm', 'QEMU ARM System Emulation', 0);
INSERT INTO qemuSystems VALUES (3, 'cris', 'QEMU CRIS System Emulation', 0);
INSERT INTO qemuSystems VALUES (4, 'm68k', 'QEMU Motorola 68k System Emulation', 0);
INSERT INTO qemuSystems VALUES (5, 'mips', 'QEMU MIPS System Emulation', 0);
INSERT INTO qemuSystems VALUES (6, 'mipsel', 'QEMU MIPS (Little Endian) System Emulation', 0);
INSERT INTO qemuSystems VALUES (7, 'mips64', 'QEMU MIPS 64bit System Emulation', 0);
INSERT INTO qemuSystems VALUES (8, 'mips64el', 'QEMU MIPS 64bit (Little Endian) System Emulation', 0);
INSERT INTO qemuSystems VALUES (9, 'ppc', 'QEMU PowerPC System Emulation', 0);
INSERT INTO qemuSystems VALUES (10, 'ppc64', 'QEMU PowerPC 64bit System Emulation', 0);
INSERT INTO qemuSystems VALUES (11, 'ppcemb', 'QEMU PowerPC Embedded System Emulation', 0);
INSERT INTO qemuSystems VALUES (12, 'sh4', 'QEMU SH4 System Emulation', 0);
INSERT INTO qemuSystems VALUES (13, 'sh4eb', 'QEMU SH4 (Big Endian) System Emulation', 0);
INSERT INTO qemuSystems VALUES (14, 'sparc', 'QEMU SPARC System Emulation', 0);
INSERT INTO qemuSystems VALUES (15, 'x86', 'QEMU x86 System Emulation', 0);
INSERT INTO qemuSystems VALUES (16, 'x86_64', 'QEMU x86-64 System Emulation', 0);
INSERT INTO qemuSystems VALUES (17, 'z80', 'QEMU Z80 System Emulation', 0);
/* x86 system emulators */
INSERT INTO qemuSystems VALUES (18, 'pc', 'PCI PC', 15);
INSERT INTO qemuSystems VALUES (19, 'isapc', 'ISA PC', 15);
/* x86-64 system emulators */
INSERT INTO qemuSystems VALUES (20, 'pc64', 'PCI PC (x86-64)', 16);
INSERT INTO qemuSystems VALUES (21, 'isapc64', 'ISA PC (x86-64)', 16);
/* ARM system emulators */
INSERT INTO qemuSystems VALUES (22, 'integratorcp', 'ARM Integrator/CP (ARM926EJ-S)', 2);
INSERT INTO qemuSystems VALUES (23, 'versatilepb', 'ARM Versatile/PB (ARM926EJ-S)', 2);
INSERT INTO qemuSystems VALUES (24, 'versatileab', 'ARM Versatile/AB (ARM926EJ-S)', 2);
INSERT INTO qemuSystems VALUES (25, 'realview', 'ARM RealView Emulation Baseboard (ARM926EJ-S)', 2);
INSERT INTO qemuSystems VALUES (26, 'akita', 'Akita PDA (PXA270)', 2);
INSERT INTO qemuSystems VALUES (27, 'spitz', 'Spitz PDA (PXA270)', 2);
INSERT INTO qemuSystems VALUES (28, 'borzoi', 'Borzoi PDA (PXA270)', 2);
INSERT INTO qemuSystems VALUES (29, 'terrier', 'Terrier PDA (PXA270)', 2);
INSERT INTO qemuSystems VALUES (30, 'sx1-v1', 'Siemens SX1 (OMAP310) V1', 2);
INSERT INTO qemuSystems VALUES (31, 'sx1', 'Siemens SX1 (OMAP310) V2', 2);
INSERT INTO qemuSystems VALUES (32, 'cheetah', 'Palm Tungsten|E aka. Cheetah PDA (OMAP310)', 2);
INSERT INTO qemuSystems VALUES (33, 'n800', 'Nokia N800 tablet aka. RX-34 (OMAP2420)', 2);
INSERT INTO qemuSystems VALUES (34, 'n810', 'Nokia N810 tablet aka. RX-44 (OMAP2420)', 2);
INSERT INTO qemuSystems VALUES (35, 'lm3s811evb', 'Stellaris LM3S811EVB', 2);
INSERT INTO qemuSystems VALUES (36, 'lm3s6965evb', 'Stellaris LM3S6965EVB', 2);
INSERT INTO qemuSystems VALUES (37, 'connex', 'Gumstix Connex (PXA255)', 2);
INSERT INTO qemuSystems VALUES (38, 'verdex', 'Gumstix Verdex (PXA270)', 2);
INSERT INTO qemuSystems VALUES (39, 'mainstone', 'Mainstone II (PXA27x)', 2);
INSERT INTO qemuSystems VALUES (40, 'musicpal', 'Marvell 88w8618 / MusicPal (ARM926EJ-S)', 2);
INSERT INTO qemuSystems VALUES (41, 'tosa', 'Tosa PDA (PXA255)', 2);
/* CRIS system emulators */
INSERT INTO qemuSystems VALUES (42, 'bareetraxfs', 'Bare ETRAX FS board', 3);
INSERT INTO qemuSystems VALUES (43, 'axis-dev88', 'AXIS devboard 88', 3);
/* Motorola 68k system emulators */
INSERT INTO qemuSystems VALUES (44, 'mcf5208evb', 'MCF5206EVB', 4);
INSERT INTO qemuSystems VALUES (45, 'an5206', 'Arnewsh 5206', 4);
INSERT INTO qemuSystems VALUES (46, 'dummy', 'Dummy board', 4);
/* MIPS system emulators */
INSERT INTO qemuSystems VALUES (47, 'malta', 'MIPS Malta Core LV', 5);
INSERT INTO qemuSystems VALUES (48, 'magnum', 'MIPS Magnum', 5);
INSERT INTO qemuSystems VALUES (49, 'pica61', 'Acer Pica 61', 5);
INSERT INTO qemuSystems VALUES (50, 'mipssim', 'MIPS MIPSsim platform', 5);
INSERT INTO qemuSystems VALUES (51, 'mips', 'mips r4k platform', 5);
/* MIPSle system emulators */
INSERT INTO qemuSystems VALUES (52, 'malta', 'MIPS Malta Core LV', 6);
INSERT INTO qemuSystems VALUES (53, 'magnum', 'MIPS Magnum', 6);
INSERT INTO qemuSystems VALUES (54, 'pica61', 'Acer Pica 61', 6);
INSERT INTO qemuSystems VALUES (55, 'mipssim', 'MIPS MIPSsim platform', 6);
INSERT INTO qemuSystems VALUES (56, 'mips', 'mips r4k platform', 6);
/* MIPS64 system emulators */
INSERT INTO qemuSystems VALUES (57, 'malta', 'MIPS Malta Core LV', 7);
INSERT INTO qemuSystems VALUES (58, 'magnum', 'MIPS Magnum', 7);
INSERT INTO qemuSystems VALUES (59, 'pica61', 'Acer Pica 61', 7);
INSERT INTO qemuSystems VALUES (60, 'mipssim', 'MIPS MIPSsim platform', 7);
INSERT INTO qemuSystems VALUES (61, 'mips', 'mips r4k platform', 7);
/* MIPS64le system emulators */
INSERT INTO qemuSystems VALUES (62, 'malta', 'MIPS Malta Core LV', 8);
INSERT INTO qemuSystems VALUES (63, 'magnum', 'MIPS Magnum', 8);
INSERT INTO qemuSystems VALUES (64, 'pica61', 'Acer Pica 61', 8);
INSERT INTO qemuSystems VALUES (65, 'mipssim', 'MIPS MIPSsim platform', 8);
INSERT INTO qemuSystems VALUES (66, 'mips', 'mips r4k platform', 8);
/* PowerPC system emulators */
INSERT INTO qemuSystems VALUES (67, 'g3beige', 'Heathrow based Power Macintosh', 9);
INSERT INTO qemuSystems VALUES (68, 'mac99', 'Mac99 based Power Macintosh AC', 9);
INSERT INTO qemuSystems VALUES (69, 'prep', 'PowerPC PReP platform', 9);
INSERT INTO qemuSystems VALUES (70, 'ref405ep', 'ref405ep', 9);
INSERT INTO qemuSystems VALUES (71, 'taihu', 'taihu', 9);
INSERT INTO qemuSystems VALUES (72, 'bamboo', 'bamboo', 9);
INSERT INTO qemuSystems VALUES (73, 'mpc8544ds', 'mpc8544ds', 9);
/* PowerPC 64bit system emulators */
INSERT INTO qemuSystems VALUES (74, 'g3beige', 'Heathrow based Power Macintosh', 10);
INSERT INTO qemuSystems VALUES (75, 'mac99', 'Mac99 based Power Macintosh AC', 10);
INSERT INTO qemuSystems VALUES (76, 'prep', 'PowerPC PReP platform', 10);
INSERT INTO qemuSystems VALUES (77, 'ref405ep', 'ref405ep', 10);
INSERT INTO qemuSystems VALUES (78, 'taihu', 'taihu', 10);
INSERT INTO qemuSystems VALUES (79, 'bamboo', 'bamboo', 10);
INSERT INTO qemuSystems VALUES (80, 'mpc8544ds', 'mpc8544ds', 10);
/* PowerPC Embedded system emulators */
INSERT INTO qemuSystems VALUES (81, 'g3beige', 'Heathrow based Power Macintosh', 11);
INSERT INTO qemuSystems VALUES (82, 'mac99', 'Mac99 based Power Macintosh AC', 11);
INSERT INTO qemuSystems VALUES (83, 'prep', 'PowerPC PReP platform', 11);
INSERT INTO qemuSystems VALUES (84, 'ref405ep', 'ref405ep', 11);
INSERT INTO qemuSystems VALUES (85, 'taihu', 'taihu', 11);
INSERT INTO qemuSystems VALUES (86, 'bamboo', 'bamboo', 11);
INSERT INTO qemuSystems VALUES (87, 'mpc8544ds', 'mpc8544ds', 11);
/* SH4 system emulators */
INSERT INTO qemuSystems VALUES (88, 'shix', 'shix card', 12);
INSERT INTO qemuSystems VALUES (89, 'r2d', 'r2d-plus board', 12);
/* SH4eb system emulators */
INSERT INTO qemuSystems VALUES (90, 'shix', 'shix card', 13);
INSERT INTO qemuSystems VALUES (91, 'r2d', 'r2d-plus board', 13);
/* SPARC system emulators */
INSERT INTO qemuSystems VALUES (92, 'SS-5', 'SPARCstation 5 (sun4m)', 14);
INSERT INTO qemuSystems VALUES (93, 'SS-10', 'SPARCstation 10 (sun4m)', 14);
INSERT INTO qemuSystems VALUES (94, 'SS-600MP', 'SPARCserver 600MP (sun4m)', 14);
INSERT INTO qemuSystems VALUES (95, 'SS-20', 'SPARCstation 20 (sun4m)', 14);
INSERT INTO qemuSystems VALUES (96, 'SS-2', 'SPARCstation 2 (sun4c)', 14);
INSERT INTO qemuSystems VALUES (97, 'Voyager', 'SPARCstation Voyager (sun4m)', 14);
INSERT INTO qemuSystems VALUES (98, 'LX', 'SPARCstation LX (sun4m)', 14);
INSERT INTO qemuSystems VALUES (99, 'SS-4', 'SPARCstation 4 (sun4m)', 14);
INSERT INTO qemuSystems VALUES (100, 'SPARCClassic', 'SPARCClassic (sun4m)', 14);
INSERT INTO qemuSystems VALUES (101, 'SPARCbook', 'SPARCbook (sun4m)', 14);
INSERT INTO qemuSystems VALUES (102, 'SS-1000', 'SPARCserver 1000 (sun4d)', 14);
INSERT INTO qemuSystems VALUES (103, 'SS-2000', 'SPARCcenter 2000 (sun4d)', 14);
/* ENDS qemuSystem INSERTs */

/* THIS SHOULD BE USED AS A BITMASK */
/* STARTS qemuSoundCards INSERTs */
INSERT INTO qemuSoundCards VALUES ('0', 'default', 'Default sound emulation');
UPDATE qemuSoundCards SET qsndId = '0' WHERE qsndName = 'default' LIMIT 1;
INSERT INTO qemuSoundCards VALUES (1, 'pcspk', 'PC speaker');
INSERT INTO qemuSoundCards VALUES (2, 'sb16', 'Creative Sound Blaster 16');
INSERT INTO qemuSoundCards VALUES (4, 'ac97', 'Intel 82801AA AC97 Audio');
INSERT INTO qemuSoundCards VALUES (8, 'es1370', 'ENSONIQ AudioPCI ES1370');
/* ENDS qemuSoundCards INSERTs */

/* STARTS qemuVGAs INSERTs */
INSERT INTO qemuVGAs VALUES ('0', 'none', 'No VGA');
UPDATE qemuVGAs SET qvgaId = '0' WHERE qvgaName = 'none' LIMIT 1;
INSERT INTO qemuVGAs VALUES (1, 'bochs', 'BOCHS VGA');
INSERT INTO qemuVGAs VALUES (2, 'cirrus', 'Cirrus GD-5446');
INSERT INTO qemuVGAs VALUES (3, 'vmware', 'VMWare Virtual VGA');
INSERT INTO qemuVGAs VALUES (4, 'xenfb', 'XEN Framebuffer');
/* ENDS qemuVGAs INSERTs */