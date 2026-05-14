'use strict';

/** ============================ Beam Analysis Data Type ============================ */

/**
 * Beam material specification.
 *
 * @param {String} name         Material name
 * @param {Object} properties   Material properties {EI : 0, GA : 0, ....}
 */
class Material {
    constructor(name, properties) {
        this.name = name;
        this.properties = properties;
    }
}

/**
 *
 * @param {Number} primarySpan          Beam primary span length
 * @param {Number} secondarySpan        Beam secondary span length
 * @param {Material} material           Beam material object
 */
class Beam {
    constructor(primarySpan, secondarySpan, material) {
        this.primarySpan = primarySpan;
        this.secondarySpan = secondarySpan;
        this.material = material;
    }
}

/** ============================ Beam Analysis Class ============================ */

class BeamAnalysis {
    constructor() {
        this.options = {
            condition: 'simply-supported'
        };

        this.analyzer = {
            'simply-supported': new BeamAnalysis.analyzer.simplySupported(),
            'two-span-unequal': new BeamAnalysis.analyzer.twoSpanUnequal()
        };
    }
    /**
     *
     * @param {Beam} beam
     * @param {Number} load
     */
    getDeflection(beam, load, condition) {
        var analyzer = this.analyzer[condition];

        if (analyzer) {
            return {
                beam: beam,
                load: load,
                equation: analyzer.getDeflectionEquation(beam, load)
            };
        } else {
            throw new Error('Invalid condition');
        }
    }
    getBendingMoment(beam, load, condition) {
        var analyzer = this.analyzer[condition];

        if (analyzer) {
            return {
                beam: beam,
                load: load,
                equation: analyzer.getBendingMomentEquation(beam, load)
            };
        } else {
            throw new Error('Invalid condition');
        }
    }
    getShearForce(beam, load, condition) {
        var analyzer = this.analyzer[condition];

        if (analyzer) {
            return {
                beam: beam,
                load: load,
                equation: analyzer.getShearForceEquation(beam, load)
            };
        } else {
            throw new Error('Invalid condition');
        }
    }
}




/** ============================ Beam Analysis Analyzer ============================ */

/**
 * Available analyzers for different conditions
 */
BeamAnalysis.analyzer = {};

/**
 * Calculate deflection, bending stress and shear stress for a simply supported beam
 *
 * @param {Beam}   beam   The beam object
 * @param {Number}  load    The applied load
 */
BeamAnalysis.analyzer.simplySupported = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }

    getShearForceEquation(beam, load) {
        var L  = beam.primarySpan;      // dalam meter
        var w  = load;                  // kN/m
        var RA = (w * L) / 2;           // kN

        return function (x) {
            // V(x) = RA - w*x  (dalam kN, x dalam meter)
            var y = RA - w * x;
            return { x: x, y: y };
        };
    }

    getBendingMomentEquation(beam, load) {
        var L  = beam.primarySpan;
        var w  = load;

        return function (x) {
            // M(x) = -(w*x/2)*(L - x)  → negatif sesuai Excel
            var y = -((w * x) / 2) * (L - x);
            return { x: x, y: y };
        };
    }

    getDeflectionEquation(beam, load) {
        var L  = beam.primarySpan;              // meter
        var EI = beam.material.properties.EI / 1e9;  // N-mm² → kN-m²
        var w  = load;                          // kN/m
        var j2 = 2;                             // default, atau dari input

        return function (x) {
            // δ(x) = -(j2 * w*x * (L³ - 2L*x² + x³)) / (24*EI) * 1000  (mm)
            var y = -(j2 * w * x * (Math.pow(L, 3) - 2 * L * Math.pow(x, 2) + Math.pow(x, 3))) / (24 * EI) * 1000;
            return { x: x, y: y };
        };
    }
};


/**
 * Calculate deflection, bending stress and shear stress for a beam with two spans of equal condition
 *
 * @param {Beam}   beam   The beam object
 * @param {Number}  load    The applied load
 */
BeamAnalysis.analyzer.twoSpanUnequal = class {
    constructor(beam, load) {
        this.beam = beam;
        this.load = load;
    }

    _getReactions(beam, load) {
        var L1 = beam.primarySpan;
        var L2 = beam.secondarySpan;
        var w  = load;

        // Three Moment Theorem
        var M1 = -(w / 8) * (L1 * L1 + L2 * L2);
        var R1 = (w * L1) / 2 + M1 / L1;
        var R3 = (w * L2) / 2 + M1 / L2;
        var R2 = w * (L1 + L2) - R1 - R3;

        return { R1, R2, R3, M1 };
    }

    getShearForceEquation(beam, load) {
        var L1 = beam.primarySpan;
        var r  = this._getReactions(beam, load);

        return function (x) {
            var y;
            if (x < L1) {
                y = r.R1 - load * x;
            } else if (x === L1) {
                // Titik tumpuan tengah — ada lompatan
                y = r.R1 - load * x + r.R2;
            } else {
                y = r.R1 + r.R2 - load * x;
            }
            return { x: x, y: y };
        };
    }

    getBendingMomentEquation(beam, load) {
        var L1 = beam.primarySpan;
        var r  = this._getReactions(beam, load);

        return function (x) {
            var y;
            if (x <= L1) {
                y = r.R1 * x - (load * x * x) / 2;
            } else {
                var x2 = x - L1;
                y = r.R1 * x - (load * x * x) / 2 + r.R2 * x2;
            }
            return { x: x, y: y };
        };
    }

    getDeflectionEquation(beam, load) {
        var L1 = beam.primarySpan;
        var L2 = beam.secondarySpan;
        var EI = beam.material.properties.EI / 1e9;  // kN-m²
        var r  = this._getReactions(beam, load);
        var j2 = 4; // dari Excel contoh

        return function (x) {
            var y;
            if (x <= L1) {
                // Span 1: defleksi dari R1 dan beban w
                var C1 = -(r.R1 * Math.pow(L1, 2) / 6 - load * Math.pow(L1, 3) / 24) / L1;
                y = (r.R1 * Math.pow(x, 3) / 6 - load * Math.pow(x, 4) / 24 + C1 * x) / EI;
            } else {
                // Span 2: x2 dari tumpuan tengah
                var x2 = x - L1;
                var C2 = -(r.R3 * Math.pow(L2, 2) / 6 - load * Math.pow(L2, 3) / 24) / L2;
                y = (r.R3 * Math.pow((L2 - x2), 3) / 6 - load * Math.pow((L2 - x2), 4) / 24 + C2 * (L2 - x2)) / EI;
                y = -y;
            }
            return { x: x, y: j2 * y * 1000 }; // konversi ke mm
        };
    }
};
